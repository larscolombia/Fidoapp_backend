<?php

namespace App\Http\Controllers\Api;

use Http;
use App\Models\Coin;
use App\Helpers\Functions;
use Illuminate\Http\Request;
use App\Models\CursoPlataforma;
use App\Models\CoursePlatformVideo;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\CoursePlatformVideoRating;
use App\Models\CoursePlatformUserProgress;
use App\Http\Requests\Api\CursoPlataformaStoreRequest;
use App\Http\Requests\Api\CursoPlataformaUpdateRequest;

class CursoPlataformaController extends Controller
{
    public function index()
    {
        $courses = CursoPlataforma::all();
        $coin = Coin::first();
        return response()->json([
            'success' => true,
            'message' => 'Cursos de la plataforma recuperados exitosamente',
            'data' => [
                'courses' => $courses->map(function ($course) use ($coin) {
                    $totalDuration = $course->videos->sum(function ($video) {
                        list($hours, $minutes, $seconds) = explode(':', $video->duration);
                        return ($hours * 3600) + ($minutes * 60) + $seconds;
                    });
                    $formatDuration = gmdate('H:i:s', $totalDuration);
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                        'description' => $course->description,
                        'image' => asset($course->image),
                        'duration' => $formatDuration,
                        'duration_text' => Functions::getDurationText($formatDuration),
                        'price' => $course->price . $coin->symbol,
                        'difficulty' => $course->difficulty,
                        'videos' => $course->videos->map(function ($video) {
                            return [
                                'id' => $video->id,
                                'title' => $video->title,
                                'thumbnail' => asset($video->thumbnail),
                                'duration' => $video->duration,
                                'duration_text' => Functions::getDurationText($video->duration), // Agregamos el campo duration_text
                                'course_platform_id' => $video->course_platform_id,
                                'url' => $video->url,
                                'video' => $video->video,
                                'visualizations' => $video->visualizations,
                                'created_at' => $video->created_at,
                                'updated_at' => $video->updated_at,
                            ];
                        })
                    ];
                }),
            ],
        ]);
    }

    public function store(Request $request)
    {
        try {
            // Validación de los datos de entrada
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

                        // Guardar cada video en la tabla CoursePlatformVideo
                        CoursePlatformVideo::create([
                            'course_platform_id' => $curso->id,
                            'url' => url('videos/cursos_plataforma/' . $videoName),
                            'video' => $videoName,
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => __('course_platform.created_successfully'),
                'data' => [
                    'course' => $curso,
                    'videos' => $curso->videos
                ]
            ], 201); // Código 201 para creación exitosa

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('course_platform.creation_failed'),
                'error' => $e->getMessage(),
            ], 500); // Código 500 para error interno del servidor
        }
    }

    public function show($id)
    {
        $course = CursoPlataforma::findOrFail($id);
        $coin = Coin::first();
        $data =   [
            'id' => $course->id,
            'name' => $course->name,
            'description' => $course->description,
            'image' => asset($course->image),
            'duration' => $course->duration,
            'price' => $course->price . $coin->symbol,
            'difficulty' => $course->difficulty,
            'duration_text' => Functions::getDurationText($course->duration),
            'videos' => $course->videos->map(function ($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'thumbnail' => $video->thumbnail,
                    'duration' => $video->duration,
                    'duration_text' => Functions::getDurationText($video->duration), // Agregamos el campo duration_text
                    'course_platform_id' => $video->course_platform_id,
                    'url' => $video->url,
                    'video' => $video->video,
                    'visualizations' => $video->visualizations,
                    'created_at' => $video->created_at,
                    'updated_at' => $video->updated_at,
                ];
            })
        ];
        return response()->json([
            'success' => true,
            'message' => 'Curso de la plataforma recuperado exitosamente',
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|between:0,99999999999999999999999999999999.99',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'duration' => 'required|integer|min:0',
                'difficulty' => 'required',
                // Validación para cada video
                'video.*' => 'sometimes|mimetypes:video/mp4,video/quicktime,video/ogg|max:20000',
            ]);

            // Encontrar el curso por ID
            $curso = CursoPlataforma::findOrFail($id);

            // Manejar la carga de la imagen (opcional)
            if ($request->hasFile('image')) {
                // Eliminar la imagen anterior si existe
                if ($curso->image) {
                    $previousImagePath = public_path($curso->image);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath); // Eliminar la imagen anterior
                    }
                }

                // Cargar la nueva imagen
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
            $curso->save(); // Guardar los cambios

            // Manejar la carga de archivos de video (opcional)
            if ($request->hasFile('video')) {
                foreach ($request->file('video') as $video) {
                    if ($video) { // Asegurarse de que el archivo no sea nulo
                        // Generar un nombre único para cada video
                        $videoName = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
                        // Mover el video a la carpeta correspondiente
                        $video->move(public_path('videos/cursos_plataforma'), $videoName);

                        // Guardar cada video en la tabla CoursePlatformVideo
                        CoursePlatformVideo::create([
                            'course_platform_id' => $curso->id,
                            'url' => url('videos/cursos_plataforma/' . $videoName),
                            'video' => $videoName,
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => __('course_platform.updated_successfully'),
                'data' => [
                    'course' => $curso,
                    'videos' => $curso->videos
                ]
            ], 200); // Código 200 para actualización exitosa

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('course_platform.update_failed'),
                'error' => $e->getMessage(),
            ], 500); // Código 500 para error interno del servidor
        }
    }

    //Buscar por tema o palabra clave
    public function search($search = null)
    {
        if (is_null($search) || trim($search) === '') {
            $courses = CursoPlataforma::all();
        } else {
            // Dividir el término de búsqueda en palabras
            $searchTerms = explode(' ', $search);

            // Realizar la búsqueda
            $courses = CursoPlataforma::where(function ($query) use ($search) {
                // Buscar coincidencias con el término completo
                $query->where('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('name', 'LIKE', '%' . $search . '%');
            })
                ->orWhere(function ($query) use ($searchTerms) {
                    // Buscar coincidencias con cada palabra
                    foreach ($searchTerms as $term) {
                        $query->orWhere(function ($q) use ($term) {
                            $q->where('description', 'LIKE', '%' . $term . '%')
                                ->orWhere('name', 'LIKE', '%' . $term . '%');
                        });
                    }
                })
                ->get();
        }

        // Comprobar si no se encontraron resultados
        if ($courses->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron resultados para la búsqueda.',
                'data' => [],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cursos de la plataforma recuperados exitosamente',
            'data' => $courses,
        ]);
    }

    public function destroy($id)
    {
        $course = CursoPlataforma::findOrFail($id);
        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Curso de la plataforma eliminado exitosamente',
        ]);
    }

    public function deleteAllVideos($id)
    {
        try {
            // Buscar el curso por ID
            $curso = CursoPlataforma::findOrFail($id);

            // Obtener todos los videos asociados al curso
            $videos = CoursePlatformVideo::where('curso_id', $curso->id)->get();

            // Verificar si hay videos y eliminarlos
            if ($videos->isNotEmpty()) {
                foreach ($videos as $video) {
                    // Verificar si el archivo existe y eliminarlo
                    if (file_exists(public_path($video->video))) {
                        unlink(public_path($video->video)); // Eliminar el archivo del sistema
                    }
                }

                // Eliminar todos los registros de videos asociados al curso
                CoursePlatformVideo::where('curso_id', $curso->id)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => __('course_platform.videos_deleted_successfully'), // Mensaje de éxito
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('course_platform.update_failed'),
                'error' => $e->getMessage(),
            ], 500); // Código 500 para error interno del servidor
        }
    }

    public function deleteVideo($cursoId, $videoId)
    {
        try {
            // Buscar el curso por ID
            $curso = CursoPlataforma::findOrFail($cursoId);

            // Buscar el video por ID
            $video = CoursePlatformVideo::where('id', $videoId)->where('curso_id', $curso->id)->firstOrFail();

            // Verificar si el archivo existe y eliminarlo
            if (file_exists(public_path($video->url))) {
                unlink(public_path($video->url)); // Eliminar el archivo del sistema
            }

            // Eliminar el registro del video de la base de datos
            $video->delete();

            return response()->json([
                'success' => true,
                'message' => __('course_platform.video_deleted_successfully'), // Mensaje de éxito
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500); // Código 500 para error interno del servidor
        }
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

    public function ratingCoursePlatformVideo(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'review_msg' => 'required|string|max:255',
                'rating' => 'required|numeric|min:1|max:5',
                'course_platform_video_id' => 'required|exists:course_platform_videos,id',
            ]);
            if (is_null($data['rating'])) {
                $data['rating'] = 1;
            }
            if ($data['rating'] >= 3) {
                $data['status'] = 1;
            }
            $coursePlatformVideoRating = CoursePlatformVideoRating::create($data);
            return response()->json([
                'success' => true,
                'data' => $coursePlatformVideoRating,
                'message' => $coursePlatformVideoRating->status == 0 ? __('messages.comment_review') : null
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la calificación.'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getRatingCourseVideo(Request $request)
    {
        // Validación de datos
        $data = $request->validate([
            'course_platform_video_id' => 'required|exists:course_platform_videos,id',
            'user_id' => 'nullable|integer',
        ]);

        // Inicializar la consulta
        $query = CoursePlatformVideoRating::query()
            ->where('course_platform_video_id', $data['course_platform_video_id'])
            ->where('status', 1);

        // Filtrar por user_id si se proporciona
        if (isset($data['user_id']) && !is_null($data['user_id'])) {
            $query->where('course_platform_video_ratings.user_id', $data['user_id']);
        }

        // Obtener las calificaciones
        $coursePlatformVideoRatings = $query->get();

        // Manejo de errores: si no se encuentra ninguna calificación
        if ($coursePlatformVideoRatings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No ratings found for the specified user'
            ], 404);
        }

        $mappedRatings = $coursePlatformVideoRatings->map(function ($rating) {
            $coursePlatformUserProgress = CoursePlatformUserProgress::where('user_id', $rating->user->id)
                ->where('course_platform_video_id', $rating->course_platform_video_id)->first();
            return [
                'id' => $rating->id,
                'user_id' => $rating->user_id,
                'course_platform_video_id' => $rating->course_platform_video_id,
                'rating' => $rating->rating,
                'review_msg' => $rating->review_msg,
                'status' => $rating->status,
                'user_full_name' => $rating->user->full_name,
                'user_avatar' => !is_null($rating->user->profile_image) ? asset($rating->user->profile_image) : asset('images/default/default.jpg'),
                'watched' => !is_null($coursePlatformUserProgress) ? $coursePlatformUserProgress->watched : false,
                'created_at' => $rating->created_at,
                'updated_at' => $rating->updated_at
            ];
        });
        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'data' =>  $mappedRatings
        ]);
    }

    public function updateVisualization($id)
    {
        try {
            $coursePlatformVideo = CoursePlatformVideo::find($id);

            // Incrementar el campo visualizations
            $coursePlatformVideo->increment('visualizations');

            // Retornar una respuesta JSON
            return response()->json([
                'success' => true,
                'message' => 'Visualización actualizada correctamente',
                'visualizations' =>  $coursePlatformVideo->visualizations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
