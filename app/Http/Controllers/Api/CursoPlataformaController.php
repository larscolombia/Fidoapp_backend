<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CursoPlataformaStoreRequest;
use App\Http\Requests\Api\CursoPlataformaUpdateRequest;
use App\Models\CursoPlataforma;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CursoPlataformaController extends Controller
{
    public function index()
    {
        $courses = CursoPlataforma::all();
        return response()->json([
            'success' => true,
            'message' => 'Cursos de la plataforma recuperados exitosamente',
            'data' => [
                'courses' => $courses->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                        'description' => $course->description,
                        'image' => asset($course->image),
                        'duration' => $course->duration,
                        'price' => $course->price,
                        'difficulty' => $course->difficulty,
                        'videos' => $course->videos,
                    ];
                }),
            ],
        ]);
    }

    public function store(CursoPlataformaStoreRequest $request)
    {
        // Verificar si la URL del video es válida
        if (!$this->isValidVideoUrl($request->input('url'))) {
            return response()->json([
                'success' => false,
                'message' => 'La URL del video no es válida o el video no está disponible.',
            ], 422);
        }

        // Manejar la carga de la imagen
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/cursos_plataforma'), $imageName);
            $imagePath = 'images/cursos_plataforma/' . $imageName;
        } else {
            $imagePath = null;
        }

        $course = CursoPlataforma::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'url' => $request->input('url'),
            'price' => $request->input('price'),
            'duration' => $request->input('duration'),
            'image' => $imagePath,
            'difficulty' => $request->input('difficulty')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Curso de la plataforma creado exitosamente',
            'data' => $course,
        ]);
    }

    public function show($id)
    {
        $course = CursoPlataforma::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Curso de la plataforma recuperado exitosamente',
            'data' => $course,
        ]);
    }

    public function update(CursoPlataformaUpdateRequest $request, $id)
    {
        $course = CursoPlataforma::findOrFail($id);

        // Verificar si la URL del video es válida, si está presente en la solicitud
        if ($request->has('url') && !$this->isValidVideoUrl($request->input('url'))) {
            return response()->json([
                'success' => false,
                'message' => 'La URL del video no es válida o el video no está disponible.',
            ], 422);
        }

        // Filtra solo los campos presentes en la solicitud
        $data = $request->only(['name', 'description', 'url', 'price', 'duration', 'difficulty']);

        // Manejar la carga de la imagen si está presente
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/cursos_plataforma'), $imageName);
            $data['image'] = 'images/cursos_plataforma/' . $imageName;

            // Eliminar la imagen anterior si existe
            if ($course->image) {
                $oldImagePath = public_path($course->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }

        // Actualiza los campos presentes
        $course->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Curso de la plataforma actualizado exitosamente',
            'data' => $course,
        ]);
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
}
