<?php

namespace App\Http\Controllers;

use Log;
use Http;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\CursoPlataforma;
use Yajra\DataTables\DataTables;
use App\Models\CoursePlatformVideo;


class CursoPlataformaController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        $this->module_title = 'course_platform.title';
        $this->module_name = 'course_platform.title';
        $this->module_icon = 'fa-solid fa-chalkboard';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function index()
    {
        $export_import = false;

        return view('backend.course_platform.index', compact('export_import'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $courses = CursoPlataforma::query();

        $filter = $request->filter;

        $posOrder = [];

        if (isset($filter)) {
        }

        return $datatable->eloquent($courses)
            ->addColumn('action', function ($data) {
                return view('backend.course_platform.action_column', compact('data'));
            })
            // ->editColumn('url', function ($data) {
            //     return $data->url;
            // })
            ->editColumn('description', function ($data) {
                return $data->description ?? 'N/A';
            })
            ->editColumn('name', function ($data) {
                return $data->name;
            })
            ->editColumn('price', function ($data) {
                return $data->price;
            })
            ->editColumn('difficulty', function ($data) {
                $difficulty = '';
                if($data->difficulty == 1){
                    $difficulty = __('course_platform.beginner');
                }
                if($data->difficulty == 2){
                    $difficulty = __('course_platform.intermediate');
                }
                if($data->difficulty == 3){
                    $difficulty = __('course_platform.advanced');
                }
                return $difficulty;
            })
              ->orderColumns(['id'], '-:column $1')
              ->rawColumns(['action'])
              ->toJson();
    }

    public function create()
    {
        return view('backend.course_platform.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|between:0,99999999999999999999999999999999.99',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'required|integer|min:0',
            'difficulty' => 'required',
            // Validación para cada video
            'video.*' => 'sometimes|mimetypes:video/mp4,video/quicktime,video/ogg|max:20000',
        ]);

        // Manejar la carga de la imagen
        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/cursos_plataforma'), $imageName);
        }

        // Crear el curso
        $curso = CursoPlataforma::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'image' => $imageName ? 'images/cursos_plataforma/' . $imageName : null,
            'duration' => $request->input('duration'),
            'difficulty' => $request->input('difficulty'),
        ]);

        // Manejar la carga de archivos de video
        if ($request->hasFile('video')) {
            foreach ($request->file('video') as $video) {
                if ($video) { // Asegurarse de que el archivo no sea nulo
                    // Generar un nombre único para cada video
                    $videoName = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
                    // Mover el video a la carpeta correspondiente
                    $video->move(public_path('videos/cursos_plataforma'), $videoName);

                    // Generar la URL del video
                    $videoUrl = url('videos/cursos_plataforma/' . $videoName);

                    // Guardar cada video en la tabla CoursePlatformVideo
                    CoursePlatformVideo::create([
                        'course_platform_id' => $curso->id,
                        'url' => $videoUrl,
                        'video' => $videoName,
                    ]);
                }
            }
        }

        return redirect()->route('backend.course_platform.index')->with('success', __('course_platform.created_successfully'));
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
        $course_platform = CursoPlataforma::findOrFail($id);
        return view('backend.course_platform.show', compact('course_platform'));
    }

    public function edit($id)
    {
        $course_platform = CursoPlataforma::findOrFail($id);
        return view('backend.course_platform.edit', compact('course_platform'));
    }

    public function update(Request $request, $id)
    {
        // Validar la solicitud
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|between:0,99999999999999999999999999999999.99',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // La imagen es opcional en la actualización
            'duration' => 'required|integer|min:0',
            'difficulty' => 'required',
            // Validación para cada video
            'video.*' => 'sometimes|mimetypes:video/mp4,video/quicktime,video/ogg|max:20000',
        ]);

        // Buscar el curso existente
        $curso = CursoPlataforma::findOrFail($id);

        // Manejar la carga de la imagen
        if ($request->hasFile('image')) {
            // Si hay una nueva imagen, moverla y actualizar el nombre
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/cursos_plataforma'), $imageName);
            $curso->image = 'images/cursos_plataforma/' . $imageName; // Actualizar el campo de imagen
        }

        // Actualizar otros campos del curso
        $curso->name = $request->input('name');
        $curso->description = $request->input('description');
        $curso->price = $request->input('price');
        $curso->duration = $request->input('duration');
        $curso->difficulty = $request->input('difficulty');
        // Guardar los cambios en el curso
        $curso->save();

        // Manejar la carga de archivos de video
        if ($request->hasFile('video')) {
            foreach ($request->file('video') as $video) {
                if ($video) { // Asegurarse de que el archivo no sea nulo
                    // Generar un nombre único para cada video
                    $videoName = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
                    // Mover el video a la carpeta correspondiente
                    $video->move(public_path('videos/cursos_plataforma'), $videoName);

                    // Generar la URL del video
                    $videoUrl = url('videos/cursos_plataforma/' . $videoName);

                    // Guardar cada nuevo video en la tabla CoursePlatformVideo
                    CoursePlatformVideo::create([
                        'course_platform_id' => $curso->id,
                        'url' => $videoUrl,
                        'video' => $videoName,
                    ]);
                }
            }
        }

        return redirect()->route('backend.course_platform.index')->with('success', __('course_platform.updated_successfully'));
    }

    public function destroy($id)
    {
        $course = CursoPlataforma::findOrFail($id);
        $course->delete();

        return redirect()->route('backend.course_platform.index')->with('success', __('course_platform.deleted_successfully'));
    }
}
