<?php

namespace App\trait;

use App\Jobs\UserNotification;

trait Notification
{
    protected function sendNotification($senderId=null,$type,$title, $data,$user,$description,$bookingId=null)
    {
        $data = [$senderId,$type,$title,$data,$user,$description,$bookingId];

        UserNotification::dispatch($data);
    }
}
