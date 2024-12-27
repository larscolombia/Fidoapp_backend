<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Wallet;
use App\Trait\Notification;
use Illuminate\Http\Request;
use App\Models\CoursePlatformVideo;
use App\Http\Controllers\Controller;
use App\Models\CoursePlatformUserProgress;
use App\Http\Controllers\CheckoutController;
use App\Models\CoursePlatformUserSubscription;
use App\Models\CursoPlataforma;

class CoursePlatformUserController extends Controller
{
    use Notification;
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
                    'course_platform_video_id' => $data['course_platform_video_id'],
                ],
                ['watched' => $data['watched']]
            );

            // Calcular el nuevo progreso total
            $totalVideos = CoursePlatformVideo::where('course_platform_id', $data['course_platform_id'])->count();

            // Contar videos vistos utilizando whereIn
            $watchedVideos = CoursePlatformUserProgress::whereIn(
                'course_platform_video_id',
                CoursePlatformVideo::where('course_platform_id', $data['course_platform_id'])->pluck('id')
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
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'course_platform_id' => 'required|exists:courses_platform,id'
            ]);
            //buscamos el curso
            $coursePlatform = CursoPlataforma::findOrFail($data['course_platform_id']);
            $checkBalance = $this->checkBalance($request, $coursePlatform->price);
            if (!$checkBalance['success']) {
                return response()->json(['success' => false, 'error' => 'Insufficient balance'], 400);
            }
            $existSubscription = CoursePlatformUserSubscription::where('course_platform_id', $data['course_platform_id'])
                ->where('user_id', $data['user_id'])->first();
            if ($existSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'There is already a subscription',
                    'data' => $existSubscription
                ], 400);
            }
            $this->updateWallet($data['user_id'],$coursePlatform->price);
            $subscription = CoursePlatformUserSubscription::create($data);

            //notify
            $this->sendNotification('event', $subscription, [$data['user_id']], __('course_platform.buy_course'). $coursePlatform->name );
            //$this->sendNotification('subscribe',$subscription,'suscription');
            return response()->json([
                'success' => true,
                'data' => $subscription
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function allCoursesUser(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);


        // Usar paginate en lugar de get
        $coursesPlatformUser = CoursePlatformUserSubscription::where('user_id', $data['user_id'])
            ->with(['course_platform', 'user']) // Cargar relaciones para evitar N+1
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Cursos del usuario recuperados exitosamente',
            'data' => [
                'courses' => $coursesPlatformUser->map(function ($coursePlatformUser) {
                    return [
                        'id' => $coursePlatformUser->id,
                        'progress' => $coursePlatformUser->progress,
                        'name' => $coursePlatformUser->course_platform->name,
                        'description' => $coursePlatformUser->course_platform->description,
                        'image' => asset($coursePlatformUser->course_platform->image),
                        'duration' => $coursePlatformUser->course_platform->duration,
                        'price' => $coursePlatformUser->course_platform->price,
                        'difficulty' => $coursePlatformUser->course_platform->difficulty,
                        'video' => optional($coursePlatformUser->course_platform->videos->first())->url,
                        'user_id' => $coursePlatformUser->user_id,
                        'user_name' => $coursePlatformUser->user->full_name,
                        'avatar' => asset($coursePlatformUser->user->profile_image)
                    ];
                })
            ],
        ]);
    }

    private function checkBalance($request, $amount)
    {
        $chekcoutController = new CheckoutController();
        $user = User::find($request->input('user_id'));
        $wallet = Wallet::where('user_id', $user->id)->first();
        $checkBalance = $chekcoutController->checkBalance($wallet, $amount);
        return $checkBalance;
    }

    private function updateWallet($userId,$amount)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        $wallet->balance = $wallet->balance - $amount;
        $wallet->save();
    }
}
