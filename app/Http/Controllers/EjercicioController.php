<?php

namespace App\Http\Controllers;

use App\Models\Ejercicio;
use App\Models\Clase;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class EjercicioController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        $this->module_title = 'ejercicios.title';
        $this->module_name = 'ejercicios.title';
        $this->module_icon = 'fa-solid fa-dumbbell';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function index($clase_id)
    {
        $clase = Clase::findOrFail($clase_id);
        return view('backend.ejercicios.index', compact('clase'));
    }

    public function index_data(DataTables $datatable, $clase_id)
    {
        $ejercicios = Ejercicio::where('clase_id', $clase_id);

        return $datatable->eloquent($ejercicios)
            ->addColumn('name', function ($ejercicio) {
                return $ejercicio->name;
            })
            ->addColumn('description', function ($ejercicio) {
                return $ejercicio->description;
            })
            // ->addColumn('url', function ($ejercicio) {
            //     return $ejercicio->url;
            // })
            ->addColumn('action', function ($data) {
                return view('backend.ejercicios.action_column', compact('data'));
            })
            ->orderColumns(['id'], '-:column $1')
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create($claseId)
    {
        $clase = Clase::findOrFail($claseId);
        return view('backend.ejercicios.create', compact('clase'));
    }

    public function store(Request $request, $claseId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'required|file|mimes:mp4,mov,avi,wmv,flv,webm|max:20000', // Validación para el video
            'url' => 'nullable|url', // URL opcional
        ]);

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'clase_id' => $claseId,
        ];

        // Manejar la carga del archivo de video
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('videos/cursos_plataforma/clases/ejercicios/'), $videoName);
            $data['video'] = 'videos/cursos_plataforma/clases/ejercicios/' . $videoName;
        }

        // Verificar si la URL del video es válida solo si se proporciona
        $url = $request->input('url');
        if ($url && !$this->isValidVideoUrl($url)) {
            return redirect()->back()->withErrors(['url' => 'La URL del video no es válida o el video no está disponible.'])->withInput();
        }

        if ($url) {
            $data['url'] = $url;
        }

        Ejercicio::create($data);

        return redirect()->route('backend.clases.ejercicios.index', ['clase' => $claseId])->with('success', __('ejercicios.created_successfully'));
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

    public function show($claseId, $ejercicioId)
    {
        $ejercicio = Ejercicio::findOrFail($ejercicioId);
        return view('backend.ejercicios.show', compact('ejercicio'));
    }

    public function edit($claseId, $ejercicioId)
    {
        $ejercicio = Ejercicio::findOrFail($ejercicioId);
        return view('backend.ejercicios.edit', compact('ejercicio'));
    }

    public function update(Request $request, $claseId, $ejercicioId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'sometimes|file|mimes:mp4,mov,avi,wmv,flv,webm|max:20000', // Validación para el video
            'url' => 'nullable|url', // URL opcional
        ]);

        $ejercicio = Ejercicio::findOrFail($ejercicioId);

        // Inicializar el array de datos a actualizar
        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];

        // Manejar la carga del archivo de video
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('videos/cursos_plataforma/clases'), $videoName);
            $data['video'] = 'videos/cursos_plataforma/clases/' . $videoName;
        }

        // Verificar si la URL del video es válida solo si se proporciona
        $url = $request->input('url');
        if ($url && !$this->isValidVideoUrl($url)) {
            return redirect()->back()->withErrors(['url' => 'La URL del video no es válida o el video no está disponible.'])->withInput();
        }

        if ($url) {
            $data['url'] = $url;
        }

        // Actualizar el modelo con los datos
        $ejercicio->update($data);

        return redirect()->route('backend.clases.ejercicios.index', ['clase' => $claseId])->with('success', __('ejercicios.updated_successfully'));
}

    public function destroy($clase_id, $id)
    {
        $ejercicio = Ejercicio::findOrFail($id);
        $ejercicio->delete();

        return redirect()->route('backend.clases.ejercicios.index', ['clase' => $clase_id])->with('success', __('ejercicios.deleted_successfully'));
    }
}