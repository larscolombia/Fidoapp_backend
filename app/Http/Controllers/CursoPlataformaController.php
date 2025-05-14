<?php

namespace App\Http\Controllers;

use Log;
use Http;
use Carbon\Carbon;
use App\Models\Course;
use App\Helpers\Functions;
use Illuminate\Http\Request;
use App\Models\CursoPlataforma;
use Yajra\DataTables\DataTables;
use App\Models\CoursePlatformVideo;
use App\Jobs\CalculateCourseDuration;
use Modules\Currency\Models\Currency;


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
                $currencySymbol = null;
                if(is_null($data->currency_id)){
                    $currency = Currency::first();
                    if($currency){
                        $currencySymbol = $currency->currency_symbol;
                    }
                }else{
                    $currencySymbol = $data->currency->currency_symbol;
                }
                return $currencySymbol.$data->price;
            })
            ->editColumn('difficulty', function ($data) {
                $difficulty = '';
                if ($data->difficulty == 1) {
                    $difficulty = __('course_platform.beginner');
                }
                if ($data->difficulty == 2) {
                    $difficulty = __('course_platform.intermediate');
                }
                if ($data->difficulty == 3) {
                    $difficulty = __('course_platform.advanced');
                }
                return $difficulty;
            })
            ->editColumn('created_at',function ($data){
                $diff = Carbon::now()->diffInHours($data->created_at);

                if ($diff < 25) {
                    return $data->created_at->diffForHumans();
                } else {
                    return $data->created_at->isoFormat('llll');
                }
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create()
    {
        $currencies = Currency::all();
        return view('backend.course_platform.create',compact('currencies'));
    }

    //
    //
    //public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'price' => 'required|numeric|between:0,99999999999999999999999999999999.99',
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,tiff,tif,bmp,webp',
    //         'difficulty' => 'required',
    //         'currency_id' => 'required',
    //         'url_youtube.*' => 'nullable|max:255',
    //         // Validación para cada video
    //         'video.*' => 'sometimes|mimetypes:video/mp4,video/quicktime,video/ogg,video/x-msvideo,video/x-flv,video/x-matroska,video/x-ms-wmv,video/3gpp,video/3gpp2,video/mpeg,video/mp2t',
    //         'title.*' => 'required|string|max:255', // Validación para el título del video
    //         'duration_video.*' => 'required|integer|min:1', // Validación para la duración del video
    //         'thumbnail.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,tiff,tif,bmp,webp', // Validación para la miniatura
    //     ]);

    //     // Manejar la carga de la imagen
    //     $imageName = null;
    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $imageName = time() . '.' . $image->getClientOriginalExtension();
    //         $image->move(public_path('images/cursos_plataforma'), $imageName);
    //     }

    //     // Crear el curso
    //     $curso = CursoPlataforma::create([
    //         'name' => $request->input('name'),
    //         'description' => $request->input('description'),
    //         'price' => $request->input('price'),
    //         'image' => $imageName ? 'images/cursos_plataforma/' . $imageName : null,
    //         'duration' => 1,
    //         'difficulty' => $request->input('difficulty'),
    //         'currency_id' => $request->input('currency_id'),
    //     ]);

    //     // Manejar la carga de archivos de video
    //     if ($request->hasFile('video')) {
    //         foreach ($request->file('video') as $index => $video) {
    //             if ($video) { // Asegurarse de que el archivo no sea nulo
    //                 // Generar un nombre único para cada video
    //                 $videoName = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
    //                 // Mover el video a la carpeta correspondiente
    //                 $video->move(public_path('videos/cursos_plataforma'), $videoName);

    //                 // Generar la URL del video
    //                 $videoUrl = url('videos/cursos_plataforma/' . $videoName);
    //                 $videoPath = public_path('videos/cursos_plataforma/' . $videoName);
    //                 $duracionFormato = Functions::getVideoDuration($videoPath);
    //                 // Manejar la carga de la miniatura si se proporciona
    //                 $thumbnailName = null;
    //                 if ($request->hasFile('thumbnail') && isset($request->file('thumbnail')[$index])) {
    //                     $thumbnail = $request->file('thumbnail')[$index];
    //                     if ($thumbnail) {
    //                         $thumbnailName = time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
    //                         $thumbnail->move(public_path('thumbnails/cursos_plataforma'), $thumbnailName);
    //                     }
    //                 }

    //                 // Guardar cada video en la tabla CoursePlatformVideo
    //                 CoursePlatformVideo::create([
    //                     'course_platform_id' => $curso->id,
    //                     'url' => $videoUrl,
    //                     'url_youtube' => $request->input('url_youtube')[$index],
    //                     'video' => $videoName,
    //                     'title' => $request->input('title')[$index], // Guardar el título del video
    //                     'duration' => $duracionFormato,
    //                     'thumbnail' => isset($thumbnailName) ? 'thumbnails/cursos_plataforma/' . $thumbnailName : asset('images/default/default.jpg'),
    //                 ]);
    //             }
    //         }
    //     }
    //     dispatch(new CalculateCourseDuration($curso));
    //     return redirect()->route('backend.course_platform.index')->with('success', __('course_platform.created_successfully'));
    // }

      public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|between:0,99999999999999999999999999999999.99',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,tiff,tif,bmp,webp',
            'difficulty' => 'required',
            'currency_id' => 'required',
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
            'duration' => 1,
            'difficulty' => $request->input('difficulty'),
            'currency_id' => $request->input('currency_id'),
        ]);

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
        $currencies = Currency::all();
        return view('backend.course_platform.edit', compact('course_platform','currencies'));
    }
    //@deprecated
    // public function update(Request $request, $id)
    // {
    //     // Validar la solicitud
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'price' => 'required|numeric|between:0,99999999999999999999999999999999.99',
    //         'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,tiff,tif,bmp,webp',
    //         'difficulty' => 'required',
    //         'currency_id' => 'required',
    //         // Validación para cada video
    //         'url_youtube.*' => 'sometimes|max:255',
    //         'video.*' => 'sometimes|mimetypes:video/mp4,video/quicktime,video/ogg,video/x-msvideo,video/x-flv,video/x-matroska,video/x-ms-wmv,video/3gpp,video/3gpp2,video/mpeg,video/mp2t',
    //         'new_video.*' => 'sometimes|mimetypes:video/mp4,video/quicktime,video/ogg,video/x-msvideo,video/x-flv,video/x-matroska,video/x-ms-wmv,video/3gpp,video/3gpp2,video/mpeg,video/mp2t',
    //         'title.*' => 'required|string|max:255', // Validación para el título del video
    //         'duration_video.*' => 'required|integer|min:1', // Validación para la duración del video
    //         'thumbnail.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,tiff,tif,bmp,webp', // Validación para la miniatura
    //         'course_platform_id.*' => 'nullable|integer',
    //     ]);

    //     // Buscar el curso existente
    //     $curso = CursoPlataforma::findOrFail($id);

    //     // Manejar la carga de la imagen
    //     if ($request->hasFile('image')) {
    //         // Si hay una nueva imagen, moverla y actualizar el nombre
    //         $image = $request->file('image');
    //         $imageName = time() . '.' . $image->getClientOriginalExtension();
    //         $image->move(public_path('images/cursos_plataforma'), $imageName);
    //         $curso->image = 'images/cursos_plataforma/' . $imageName; // Actualizar el campo de imagen
    //     }

    //     // Actualizar otros campos del curso
    //     $curso->name = $request->input('name');
    //     $curso->description = $request->input('description');
    //     $curso->price = $request->input('price');
    //     $curso->difficulty = $request->input('difficulty');
    //     $curso->currency_id = $request->input('currency_id');
    //     // Guardar los cambios en el curso
    //     $curso->save();

    //     // Manejar la carga de archivos de video
    //     foreach ($request->input('course_platform_id') as $index => $videoId) {
    //         // Verificar si se ha subido un nuevo video
    //         if ($request->hasFile('video.' . $index)) {
    //             // Si hay un nuevo archivo de video, procesarlo
    //             $video = $request->file('video.' . $index);
    //             $duracionFormato = null;
    //             if ($video) {
    //                 // Generar un nombre único para cada video
    //                 $videoName = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
    //                 // Mover el video a la carpeta correspondiente
    //                 $video->move(public_path('videos/cursos_plataforma'), $videoName);

    //                 // Generar la URL del video
    //                 $videoUrl = url('videos/cursos_plataforma/' . $videoName);
    //                 $videoPath = public_path('videos/cursos_plataforma/' . $videoName);
    //                 $duracionFormato = Functions::getVideoDuration($videoPath);
    //             }

    //             // Manejar la carga de la miniatura si se proporciona
    //             $thumbnailName = null;
    //             if ($request->hasFile('thumbnail') && isset($request->file('thumbnail')[$index])) {
    //                 $thumbnail = $request->file('thumbnail')[$index];
    //                 if ($thumbnail) {
    //                     $thumbnailName = time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
    //                     $thumbnail->move(public_path('thumbnails/cursos_plataforma'), $thumbnailName);
    //                 }
    //             }

    //             $existingVideo = CoursePlatformVideo::find($videoId);

    //             if ($existingVideo) {
    //                 // Actualizar solo los campos necesarios si el registro existe
    //                 $updateData = [
    //                     'course_platform_id' => $curso->id,
    //                     'url' => isset($videoUrl) ? $videoUrl : null,
    //                     'video' => isset($videoName) ? $videoName : null,
    //                     'title' => $request->input('title')[$index],
    //                     'url_youtube' => $request->input('url_youtube')[$index],
    //                     'thumbnail' => isset($thumbnailName) ? 'thumbnails/cursos_plataforma/' . $thumbnailName : asset('images/default/default.jpg'),
    //                 ];

    //                 // Agregar duración solo si no es null
    //                 if ($duracionFormato !== null) {
    //                     $updateData['duration'] = $duracionFormato;
    //                 }

    //                 $existingVideo->update($updateData);
    //             } else {
    //                 // Si no existe, crear un nuevo registro
    //                 CoursePlatformVideo::create([
    //                     'id' => $videoId,
    //                     'course_platform_id' => $curso->id,
    //                     'url' => isset($videoUrl) ? $videoUrl : null,
    //                     'video' => isset($videoName) ? $videoName : null,
    //                     'title' => $request->input('title')[$index],
    //                     'url_youtube' => $request->input('url_youtube')[$index],
    //                     'duration' => $duracionFormato, // Aquí no hay problema si es null
    //                     'thumbnail' => isset($thumbnailName) ? 'thumbnails/cursos_plataforma/' . $thumbnailName : asset('images/default/default.jpg'),
    //                 ]);
    //             }
    //         } else {
    //             // Si no se subió un nuevo archivo, solo actualizar el título
    //             $thumbnailName = null;
    //             if ($request->hasFile('thumbnail') && isset($request->file('thumbnail')[$index])) {
    //                 $thumbnail = $request->file('thumbnail')[$index];
    //                 if ($thumbnail) {
    //                     $thumbnailName = time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
    //                     $thumbnail->move(public_path('thumbnails/cursos_plataforma'), $thumbnailName);
    //                 }
    //             }
    //             $defaultThumbnail = !is_null(CoursePlatformVideo::find($videoId)) && !is_null(CoursePlatformVideo::find($videoId)->thumbnail) ? CoursePlatformVideo::find($videoId)->thumbnail  : asset('images/default/default.jpg');
    //             CoursePlatformVideo::where('id', $videoId)->update([
    //                 'title' => $request->input('title')[$index],
    //                 'url_youtube' => $request->input('url_youtube')[$index],
    //                 'thumbnail' => isset($thumbnailName) ? 'thumbnails/cursos_plataforma/' . $thumbnailName : $defaultThumbnail
    //             ]);
    //         }
    //     }

    //     // Manejar nuevos videos que no tienen ID (no están en course_platform_id)
    //     if ($request->hasFile('new_video')) {
    //         foreach ($request->file('new_video') as $index => $newVideo) {
    //             if ($newVideo) {
    //                 // Generar un nombre único para cada nuevo video
    //                 $newVideoName = time() . '_' . uniqid() . '.' . $newVideo->getClientOriginalExtension();
    //                 // Mover el nuevo video a la carpeta correspondiente
    //                 $newVideo->move(public_path('videos/cursos_plataforma'), $newVideoName);

    //                 // Generar la URL del nuevo video
    //                 $newVideoUrl = url('videos/cursos_plataforma/' . $newVideoName);
    //                 $videoPath = public_path('videos/cursos_plataforma/' . $newVideoName);
    //                 $duracionFormato = Functions::getVideoDuration($videoPath);
    //                 // Manejar la carga de miniaturas si se proporciona
    //                 $newThumbnailName = null;
    //                 if ($request->hasFile('new_thumbnail') && isset($request->file('new_thumbnail')[$index])) {
    //                     $newThumbnail = $request->file('new_thumbnail')[$index];
    //                     if ($newThumbnail) {
    //                         $newThumbnailName = time() . '_' . uniqid() . '.' . $newThumbnail->getClientOriginalExtension();
    //                         $newThumbnail->move(public_path('thumbnails/cursos_plataforma'), $newThumbnailName);
    //                     }
    //                 }

    //                 CoursePlatformVideo::create([
    //                     'course_platform_id' => $curso->id,
    //                     'url' => isset($newVideoUrl) ?  $newVideoUrl : null,
    //                     'video' => isset($newVideoName) ? $newVideoName : null,
    //                     'url_youtube' => $request->input('url_youtube')[$index],
    //                     'title' => request()->input("title")[$index],  // Título del nuevo video (debe estar en el formulario)
    //                     'duration' => $duracionFormato,  // Duración del nuevo video (debe estar en el formulario)
    //                     'thumbnail' => isset($newThumbnailName) ?  $newThumbnailName : asset('images/default/default.jpg'),
    //                 ]);
    //             }
    //         }
    //     }
    //     dispatch(new CalculateCourseDuration($curso));
    //     return redirect()->route('backend.course_platform.index')->with('success', __('course_platform.updated_successfully'));
    // }

     public function update(Request $request, $id)
    {
        // Validar la solicitud
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|between:0,99999999999999999999999999999999.99',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,tiff,tif,bmp,webp',
            'difficulty' => 'required',
            'currency_id' => 'required',
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
        $curso->difficulty = $request->input('difficulty');
        $curso->currency_id = $request->input('currency_id');
        // Guardar los cambios en el curso
        $curso->save();
        return redirect()->route('backend.course_platform.index')->with('success', __('course_platform.updated_successfully'));
    }

    public function destroy($id)
    {
        $course = CursoPlataforma::findOrFail($id);
        $course->delete();

        return redirect()->route('backend.course_platform.index')->with('success', __('course_platform.deleted_successfully'));
    }
}
