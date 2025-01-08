<?php

namespace App\trait;

use App\Jobs\UserNotification;

trait Notification
{
    protected function sendNotification($type,$title, $data,$user,$description,$bookingId=null)
    {
        $data = [$type,$title,$data,$user,$description,$bookingId];

        UserNotification::dispatch($data);
    }
}
