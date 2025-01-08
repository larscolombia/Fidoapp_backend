<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\Backend\UserEventNotification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\UserNotification as UserNotificationModel;

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
        list($type,$title, $eventData, $userIds,$description,$bookingId) = $this->data;

        foreach ($userIds as $userId) {
           $userNotificationModel = UserNotificationModel::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'description' => $description,
                'is_read' => false,
                'booking_id' => $bookingId
            ]);

            event(new UserEventNotification($userNotificationModel));
        }
    }
}
