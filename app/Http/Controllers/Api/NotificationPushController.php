<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationPushController extends Controller
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendNotification($title, $body, $deviceToken)
    {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification([
                'title' => $title,
                'body' => $body,
            ]);

        $this->messaging->send($message);
    }

    public function sendNotificationStatic($title, $body, $deviceToken)
    {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification([
                'title' => $title,
                'body' => $body,
            ]);

        $this->messaging->send($message);
    }

    public function sendNotificationDev()
    {
        $message = CloudMessage::withTarget('token', 'cDlseZnmQE6VGOs0PzNIxp:APA91bGBWYYM80VN53cTwIRpLrEmJ8YGGWVm9DBIPAKVsF4QB8LyzIlDKpqVn0AHUVQ5Sf6vrYqF4RmlszYa3byqXza4DsSoVaW-WHf-cdpZ6IowOAhdQ1E')
            ->withNotification([
                'title' => 'titulo de prueba',
                'body' => 'cuerpo de prueba',
            ]);

        $this->messaging->send($message);
    }
}
