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
                'image' => asset('img/logo/mini_logo.png')
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
        $message = CloudMessage::withTarget('token', 'dzTNXtf-TwGPdpIvUQ7XM7:APA91bFlPcWvssjrFf1C_nzv8Sbr6rRO2N4DSYisfp6ln_zVe-TFVYHTHEHl7uy3cOVfyxJPnXW42Tz05s3xDG4Gmh9Cnb46UBU1ASXu8-4I7fH2Jrdrms8')
            ->withNotification([
                'title' => 'titulo de prueba',
                'body' => 'cuerpo de prueba',
            ]);

        $this->messaging->send($message);
    }
}
