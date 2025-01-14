<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Contract\Messaging;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Http\Controllers\Api\NotificationPushController;

class LostPet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $userIds, $title, $description;
    public function __construct($userIds, $title, $description)
    {
        $this->userIds = $userIds;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userIds = $this->userIds;
        foreach ($userIds as $id) {
            $this->generateNotification($this->title, $this->description, $id);
        }
    }

    //     private function generateNotification($title,$description,$userId){
    //         // Obtén el token del dispositivo del usuario específico
    //         $user = User::where('id', $userId)->whereNotNull('device_token')->first();

    //         if ($user) {
    //           try{
    //             $messaging = app(Messaging::class);
    //             $message = CloudMessage::withTarget('token', $user->device_token)
    //             ->withNotification(['title' => $title, 'body' => $description]);
    //             $messaging->send($message);
    //           }catch(\Exception $e){
    //             Log::error('Error:'.$e->getMessage());
    //           }
    //        }
    //    }

    private function generateNotification($title, $description, $userId)
    {
        // Obtén el token del dispositivo del usuario específico
        $user = User::where('id', $userId)->whereNotNull('device_token')->first();

        if ($user) {
            try {
                $pushNotificationController = new NotificationPushController(app(Messaging::class));
                //$pushNotificationController->sendNotification($title, $description, $user->device_token);
                $pushNotificationController->sendNotificationDev();
            } catch (\Exception $e) {
                Log::error('Error:' . $e->getMessage());
            }
        }
    }
}
