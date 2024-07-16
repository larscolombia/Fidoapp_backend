<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Clases\UpdateRequest;
use App\Http\Requests\Api\ClaseStoreRequest;
use App\Models\Clase;
use App\Models\CursoPlataforma;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClaseController extends Controller
{
    public function index($courseId)
    {
        $course = CursoPlataforma::findOrFail($courseId);
        $clases = $course->clases;
        return response()->json([
            'success' => true,
            'message' => 'Clases recuperadas exitosamente',
            'data' => $clases,
        ]);
    }

    public function store(ClaseStoreRequest $request, $courseId)
    {
        $course = CursoPlataforma::findOrFail($courseId);

        if ($request->input('price') > $course->price) {
            return response()->json([
                'success' => false,
                'message' => 'El precio de la clase no puede ser mayor al precio del curso',
            ], 422);
        }

        // Verificar si la URL del video es válida
        if (!$this->isValidVideoUrl($request->input('url'))) {
            return response()->json([
                'success' => false,
                'message' => 'La URL del video no es válida o el video no está disponible.',
            ], 422);
        }

        $clase = Clase::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'url' => $request->input('url'),
            'price' => $request->input('price'),
            'course_id' => $courseId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clase creada exitosamente',
            'data' => $clase,
        ]);
    }

    public function show($courseId, $id)
    {
        $clase = Clase::where('course_id', $courseId)->findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Clase recuperada exitosamente',
            'data' => $clase,
        ]);
    }

    public function update(UpdateRequest $request, $courseId, $id)
    {
        $course = CursoPlataforma::findOrFail($courseId);

        if ($request->input('price') > $course->price) {
            return response()->json([
                'success' => false,
                'message' => 'El precio de la clase no puede ser mayor al precio del curso',
            ], 422);
        }

        $clase = Clase::where('course_id', $courseId)->findOrFail($id);
        $clase->update($request->only(['name', 'description', 'url', 'price']));


        return response()->json([
            'success' => true,
            'message' => 'Clase actualizada exitosamente',
            'data' => $clase,
        ]);
    }

    public function destroy($courseId, $id)
    {
        $clase = Clase::where('course_id', $courseId)->findOrFail($id);
        $clase->delete();

        return response()->json([
            'success' => true,
            'message' => 'Clase eliminada exitosamente',
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
