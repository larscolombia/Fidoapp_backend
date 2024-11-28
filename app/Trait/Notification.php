<?php

namespace App\trait;

use App\Jobs\UserNotification;

trait Notification
{
    protected function sendNotification($title, $data,$user,$description)
    {
        $data = [$title,$data,$user,$description];

        UserNotification::dispatch($data);
    }
}
