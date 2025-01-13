<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Contract\Messaging;
use Illuminate\Queue\InteractsWithQueue;
use NotificationChannels\FCM\FCMChannel;
use NotificationChannels\FCM\FCMMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\Backend\UserEventNotification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Http\Controllers\Api\NotificationPushController;
use App\Models\UserNotification as UserNotificationModel;
use App\Notifications\UserNotification as UserNotificationEvent;

class UserNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        list($senderId, $type, $title, $eventData, $userIds, $description, $bookingId) = $this->data;

        foreach ($userIds as $userId) {
            $userNotificationModel = UserNotificationModel::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'description' => $description,
                'is_read' => false,
                'booking_id' => $bookingId,
                'sender_id' => $senderId
            ]);

           // event(new UserEventNotification($userNotificationModel));

           $this->sendNotification($userId,$title,$description);
        }
    }

    private function sendNotification($userId, $title, $description)
    {
        // Obtén el token del dispositivo del usuario específico
        $user = User::where('id', $userId)->whereNotNull('device_token')->first();

        if (!$user) {
            return response()->json(['success'=> false,'message' => 'No se encontró el token del dispositivo para este usuario.'], 404);
        }
        \Log::info('ejecucion del job');
        $pushNotificationController = new NotificationPushController(app(Messaging::class));
        $pushNotificationController->sendNotification($title, $description, $user->device_token);
    }
}
