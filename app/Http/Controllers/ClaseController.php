<?php

namespace App\Http\Controllers;

use Http;
use Validator;
use App\Models\Clase;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CursoPlataforma;
use Yajra\DataTables\DataTables;
use App\Models\CoursePlatformVideo;
use Illuminate\Support\Facades\Log;


class ClaseController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        $this->module_title = 'clases.title';
        $this->module_name = 'clases.title';
        $this->module_icon = 'fa-solid fa-chalkboard-teacher';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function index($course_id)
    {
        $course = CursoPlataforma::findOrFail($course_id);
        return view('backend.clases.index', compact('course'));
    }

    public function index_data(DataTables $datatable, $course_id)
    {
        $clases = CoursePlatformVideo::where('course_platform_id', $course_id);

        return $datatable->eloquent($clases)
            ->addColumn('action', function ($data) {
                return view('backend.clases.action_column', compact('data'));
            })
            // ->editColumn('url', function ($data) {
            //     return $data->url;
            // })
            ->editColumn('duration', function ($data) {
                return $data->duration;
            })
            ->editColumn('title', function ($data) {
                return $data->title;
            })
            ->editColumn('visualizations', function ($data) {
                return $data->visualizations;
            })
            ->orderColumns(['id'], '-:column $1')
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create()
    {
        $courses = CursoPlataforma::all();
        return view('backend.clases.create', compact('courses'));
    }

    public function store(Request $request, $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimes:mp4,mov,ogg,qt|max:20000',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'required|integer|min:1',
        ]);

        $course = CursoPlataforma::findOrFail($request->route('course'));
        // $price = $request->input('price');

        // if ($price > $course->price) {
        //     return redirect()->back()->withErrors(['price' => __('clases.El precio de la clase no puede ser mayor que el del curso al que pertenece.')])->withInput();
        // }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $videoUrl = null;
        // Manejar la carga del archivo de video
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('videos/cursos_plataforma'), $videoName);
            // Generar la URL del video
            $videoUrl = url('videos/cursos_plataforma/' . $videoName);
        }

        // Manejar la carga de la miniatura si se proporciona
        $thumbnailName = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            if ($thumbnail) {
                $thumbnailName = time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
                $thumbnail->move(public_path('thumbnails/cursos_plataforma'), $thumbnailName);
            }
        }

        // Guardar cada video en la tabla CoursePlatformVideo
        CoursePlatformVideo::create([
            'course_platform_id' => $course->id,
            'url' => $videoUrl,
            'video' => $videoName,
            'title' => $request->input('title'),
            'duration' => $request->input('duration'),
            'thumbnail' => isset($thumbnailName) ? 'thumbnails/cursos_plataforma/' . $thumbnailName : asset('images/default/default.jpg'),
        ]);

        // Clase::create([
        //     'name' => $request->name,
        //     'description' => $request->description,
        //     'url' => $url,
        //     'video' => $videoName ? 'videos/cursos_plataforma/clases/' . $videoName : null,
        //     'price' => $request->price,
        //     'course_id' => $course->id,
        // ]);

        return redirect()->route('backend.course_platform.clases.index', ['course' => $request->route('course')])->with('success', __('clases.created_successfully'));
    }

    private function isValidVideoUrl($url)
    {
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            Log::info('assasda');
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
            Log::info("sfsfsd");
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

    public function show($id)
    {
        $clase = CoursePlatformVideo::with('coursePlatform')->findOrFail($id);

        // Extraer el ID del video de la URL
        $videoId = $this->getVideoId($clase->url);

        return view('backend.clases.show', compact('clase', 'videoId'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $clase = CoursePlatformVideo::with('coursePlatform')->findOrFail($id);
        return view('backend.clases.edit', compact('clase'));
    }

    private function getVideoId($url)
    {
        if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $match)) {
            return $match[1];
        } elseif (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:vimeo\.com\/)([0-9]+)/', $url, $match)) {
            return $match[1];
        }
        return null;
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
           'video' => 'sometimes|file|mimes:mp4,mov,ogg,qt|max:20000',
            'thumbnail' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $course_platform = CoursePlatformVideo::findOrFail($id);

        $videoUrl = null;
        $videoName = null;
        // Manejar la carga del archivo de video
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('videos/cursos_plataforma'), $videoName);
            // Generar la URL del video
            $videoUrl = url('videos/cursos_plataforma/' . $videoName);
        }

        // Manejar la carga de la miniatura si se proporciona
        $thumbnailName = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            if ($thumbnail) {
                $thumbnailName = time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
                $thumbnail->move(public_path('thumbnails/cursos_plataforma'), $thumbnailName);
            }
        }

        // Actualizar el modelo con los datos
        $course_platform->update([
            'title' => $request->input('title',$course_platform->title),
            'duration' => $request->input('duration',$course_platform->duration),
            'url' => !is_null($videoUrl) ? $videoUrl : $course_platform->url,
            'video' => !is_null($videoName) ? $videoName :  $course_platform->video,
            'thumbnail' => !is_null($thumbnailName) ? $thumbnailName : $course_platform->thumbnail
        ]);

        return redirect()->route('backend.course_platform.index')->with('success', __('course_platform.updated_successfully'));
    }

    public function destroy($course_id, $id)
    {
        $clase = CoursePlatformVideo::findOrFail($id);
        $clase->delete();

        return redirect()->route('backend.course_platform.clases.index', ['course' => $course_id])->with('success', __('clases.deleted_successfully'));
    }
}
