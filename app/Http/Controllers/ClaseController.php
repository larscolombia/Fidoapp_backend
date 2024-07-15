<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use App\Models\CursoPlataforma;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Validator;


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
        $clases = Clase::where('course_id', $course_id);

        return $datatable->eloquent($clases)
            ->addColumn('action', function ($data) {
                return view('backend.clases.action_column', compact('data'));
            })
            ->editColumn('url', function ($data) {
                return $data->url;
            })
            ->editColumn('description', function ($data) {
                return Str::limit($data->description, 50, '...');
            })
            ->editColumn('name', function ($data) {
                return $data->name;
            })  
            ->editColumn('price', function ($data) {
                return $data->price;
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

    public function store(Request $request , $course)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'required|url',
            'price' => 'required|numeric',
        ]);

        $course = CursoPlataforma::findOrFail($request->route('course'));
        $price = $request->input('price');

        if ($price > $course->price) {
            return redirect()->back()->withErrors(['price' => __('clases.El precio de la clase no puede ser mayor que el del curso al que pertenece.')])->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $url = $request->input('url');

        if (!$this->isValidVideoUrl($url)) {
            return redirect()->back()->withErrors(['url' => 'Video inválido o no soportado. Por favor, ingrese un enlace de YouTube o Vimeo.'])->withInput();
        }

        Clase::create([
            'name' => $request->name,
            'description' => $request->description,
            'url' => $request->url,
            'price' => $request->price,
            'course_id' => $course->id,
        ]);

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
        $clase = Clase::findOrFail($id);

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
        $clase = Clase::with('cursoPlataforma')->findOrFail($id);
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'required|url',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $url = $request->input('url');

        if (!$this->isValidVideoUrl($url)) {
            return redirect()->back()->withErrors(['url' => 'Video inválido o no soportado. Por favor, ingrese un enlace de YouTube o Vimeo.'])->withInput();
        }

        $course_platform = CursoPlataforma::findOrFail($id);
        $course_platform->update([
            'name' => $request->name,
            'description' => $request->description,
            'url' => $request->url,
            'price' => $request->price,
        ]);

        return redirect()->route('backend.course_platform.index')->with('success', __('course_platform.updated_successfully'));
    }

    public function destroy($course_id, $id)
    {
        $clase = Clase::findOrFail($id);
        $clase->delete();

        return redirect()->route('backend.clases.index', ['course' => $course_id])->with('success', __('clases.deleted_successfully'));
    }
}
