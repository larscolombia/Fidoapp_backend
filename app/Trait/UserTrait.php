<?php

namespace App\Trait;
use App\Jobs\BulkNotification;

trait UserTrait
{
    protected function sendNotificationUser($type, $user)
    {
        $data = mail_footer($type, $user);

        // $address = [
        //     'address_line_1' => $booking->branch->address->address_line_1,
        //     'address_line_2' => $booking->branch->address->address_line_2,
        //     'city' => $booking->branch->address->city,
        //     'state' => $booking->branch->address->state,
        //     'country' => $booking->branch->address->country,
        //     'postal_code' => $booking->branch->address->postal_code,
        // ];

        $data['user'] = $user;
        // dd($data);

        BulkNotification::dispatch($data);
    }
}
