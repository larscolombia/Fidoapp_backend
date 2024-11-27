<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CoursePlatformVideo;
use App\Http\Controllers\Controller;
use App\Models\CoursePlatformUserProgress;
use App\Models\CoursePlatformUserSubscription;

class CoursePlatformUserController extends Controller
{
    public function markVideoAsWatched(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'course_platform_id' => 'required|exists:courses_platform,id',
                'course_platform_video_id' => 'required|exists:course_platform_videos,id',
                'watched' => 'required|boolean'
            ]);
            // Verificar si el usuario está suscrito al curso
            $subscription = CoursePlatformUserSubscription::where('user_id', $data['user_id'])
                ->where('course_platform_id', $data['course_platform_id'])
                ->firstOrFail();

            // Marcar el video como visto
            CoursePlatformUserProgress::updateOrCreate(
                [
                    'user_id' => $data['user_id'],
                    'course_platform_video_id' =>$data['course_platform_video_id'],
                ],
                ['watched' => $data['watched']]
            );

            // Calcular el nuevo progreso total
            $totalVideos = CoursePlatformVideo::where('course_platform_id ', $data['course_platform_id'])->count();

            // Contar videos vistos utilizando whereIn
            $watchedVideos = CoursePlatformUserProgress::whereIn(
                'course_platform_video_id',
                CoursePlatformVideo::where('course_platform_id ', $data['course_platform_id'])->pluck('id')
            )
                ->where('user_id', $data['user_id'])
                ->where('watched', true)
                ->count();

            // Calcular porcentaje
            $newProgress = ($totalVideos > 0) ? ($watchedVideos / $totalVideos) * 100 : 0;

            // Actualizar progreso en la suscripción
            $subscription->progress = (int)$newProgress;
            $subscription->save();

            return response()->json([
                'success' => true,
                'message' => __('Video marcado como visto'),
                'progress' => (int)$newProgress,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => __('Suscripción no encontrada'),
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error al marcar video como visto'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function subscribe(Request $request)
    {
        try{
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'course_platform_id' => 'required|exists:courses_platform,id'
            ]);
            $subscription = CoursePlatformUserSubscription::create($data);
            return response()->json([
                'success' => true,
                'data' => $subscription
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
