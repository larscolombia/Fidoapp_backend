<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Http;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CourseController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Courses.title';
        // Module name
        $this->module_name = 'Courses.title';

        // Module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function index()
    {
        $export_import = false;

        return view('backend.courses.index', compact('export_import'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $courses = Course::query();

        return $datatable->eloquent($courses)
            ->addColumn('action', function ($data) {
                return view('backend.courses.action_column', compact('data'));
            })
            ->editColumn('enlace', function ($data) {
                return $data->enlace;
            })
            ->editColumn('titulo', function ($data) {
                return $data->titulo;
            })
            ->make(true);
    }

    public function create()
    {
        return view('backend.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'enlace' => 'required|url|max:255',
        ]);

        // Verificar que el video existe y tiene vista previa
        $videoUrl = $request->input('enlace');
        if (!$this->isValidVideoUrl($videoUrl)) {
            return redirect()->back()
                             ->withErrors(['enlace' => __('Courses.The video URL is invalid or the video cannot be previewed.')])
                             ->withInput();
        }

        Course::create([
            'titulo' => $request->input('titulo'),
            'enlace' => $request->input('enlace'),
        ]);

        return redirect()->route('backend.courses.index')
                         ->with('success', __('Courses.Course has been created successfully'));
    }

    public function show(Course $course)
    {
        return view('backend.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        return view('backend.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'enlace' => 'required|url|max:255',
        ]);

        $videoUrl = $request->input('enlace');
        if (!$this->isValidVideoUrl($videoUrl)) {
            return redirect()->back()
                             ->withErrors(['enlace' => __('Courses.The video URL is invalid or the video cannot be previewed.')])
                             ->withInput();
        }

        $course->update([
            'titulo' => $request->input('titulo'),
            'enlace' => $request->input('enlace'),
        ]);

        return redirect()->route('backend.courses.index')
                         ->with('success', __('Courses.Course has been updated successfully'));
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('backend.courses.index')
                         ->with('success', __('Courses.Course has been deleted successfully'));
    }

    private function isValidVideoUrl($url)
    {
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            $videoId = $this->getYouTubeVideoId($url);
            return $this->canPreviewYouTubeVideo($videoId);
        } elseif (strpos($url, 'vimeo.com') !== false) {
            $videoId = $this->getVimeoVideoId($url);
            return $this->canPreviewVimeoVideo($videoId);
        }

        return false;
    }

    private function getYouTubeVideoId($url)
    {
        $regex = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
        preg_match($regex, $url, $matches);
        return $matches[1] ?? null;
    }

    private function getVimeoVideoId($url)
    {
        $regex = '/(?:https?:\/\/)?(?:www\.)?(?:vimeo\.com\/)([0-9]+)/';
        preg_match($regex, $url, $matches);
        return $matches[1] ?? null;
    }

    private function canPreviewYouTubeVideo($videoId)
    {
        if ($videoId) {
            $response = Http::get("https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=$videoId&format=json");
            return $response->ok();
        }
        return false;
    }

    private function canPreviewVimeoVideo($videoId)
    {
        if ($videoId) {
            $response = Http::get("https://vimeo.com/api/oembed.json?url=https://vimeo.com/$videoId");
            return $response->ok();
        }
        return false;
    }

    public function get()
    {
        $courses = Course::all();
        
        return response()->json([
            'success' => true,
            'message' => __('messages.courses_retrieved_successfully'),
            'data' => $courses
        ]);
    }

    public function getById($id)
    {
        $course = Course::find($id);

        if ($course) {
            return response()->json([
                'success' => true,
                'message' => __('messages.course_retrieved_successfully'),
                'data' => $course
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.course_not_found')
            ], 404);
        }
    }
}
