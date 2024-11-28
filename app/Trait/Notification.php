<?php

namespace App\trait;

use App\Jobs\BulkNotification;

trait Notification
{
    protected function sendNotification($type, $data,$service)
    {
        $array = mail_footer($type, $data);

        $array[$service] = $data;

        BulkNotification::dispatch($array);
    }
}
