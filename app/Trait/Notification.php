<?php

namespace App\trait;

use App\Jobs\UserNotification;

trait Notification
{
    protected function sendNotification($type,$title, $data,$user,$description)
    {
        $data = [$type,$title,$data,$user,$description];

        UserNotification::dispatch($data);
    }
}
