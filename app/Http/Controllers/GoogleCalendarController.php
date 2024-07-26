<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Carbon\Carbon;
use Modules\Event\Models\Event;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Illuminate\Support\Facades\Log;

class GoogleCalendarController extends Controller
{
    protected $client;

    public function __construct()
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = config('services.google.redirect');

        // Debugging: Verificar si las variables están vacías
        if (empty($clientId) || empty($clientSecret) || empty($redirectUri)) {
            Log::info('Client ID: ' . $clientId . ' Client Secret: ' . $clientSecret .  ' Redirect URI: ' . $redirectUri);
        }

        $this->client = new Google_Client();
        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
        $this->client->setRedirectUri($redirectUri);
        $this->client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
    }

    public function redirectToGoogle()
    {
        $authUrl = $this->client->createAuthUrl();
        return redirect()->to($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $this->client->authenticate($request->input('code'));
        $token = $this->client->getAccessToken();
        $request->session()->put('google_calendar_token', $token);

        return redirect()->route('backend.events.index')->with('success', __('event.Google Calendar connected successfully'));
    }

    public function createEvent(Request $request)
    {
        $token = $request->session()->get('google_calendar_token');

        if ($token) {
            $this->client->setAccessToken($token);

            if ($this->client->isAccessTokenExpired()) {
                $refreshToken = $this->client->getRefreshToken();
                $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                $request->session()->put('google_calendar_token', $this->client->getAccessToken());
            }

            $service = new Google_Service_Calendar($this->client);

            $event = Event::find($request->input('id_event'));

            // Convertir las fechas al formato ISO 8601 con Carbon
            $date_event = $event->date; // Ejemplo: "2024-08-06 00:00:00"
            $end_date_event = $event->end_date; // Ejemplo: "2024-08-06 02:00:00"
            
            // Asegurarse de que los datos recibidos no sean nulos o vacíos
            if (empty($date_event) || empty($end_date_event)) {
                return redirect()->back()->with('error', 'Fechas no proporcionadas o incorrectas.');
            }

            // Formatear fechas correctamente
            $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date_event, 'America/Bogota')
                                    ->format(\DateTime::ATOM);
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $end_date_event, 'America/Bogota')
                                    ->format(\DateTime::ATOM);

            // Crear un nuevo evento de Google Calendar
            $event = new Google_Service_Calendar_Event([
                'summary' => $event->name,
                'location' => $event->location,
                'description' => $request->input('description_event'),
                'start' => new Google_Service_Calendar_EventDateTime([
                    'dateTime' => $startDateTime,
                    'timeZone' => 'America/Bogota',
                ]),
                'end' => new Google_Service_Calendar_EventDateTime([
                    'dateTime' => $endDateTime,
                    'timeZone' => 'America/Bogota',
                ]),
            ]);

            // Insertar el evento en Google Calendar
            try {
                $calendarId = 'primary';
                $createdEvent = $service->events->insert($calendarId, $event);

                if($createdEvent) {
                    CalendarEvent::create([
                        'user_id' => auth()->id(),
                        'event_id' => $request->input('id_event'),
                        'url' => $createdEvent->htmlLink,
                        'proveedor' => 'google',
                    ]);
                }

                return redirect()->back()->with('success', __('event.Event added to Google Calendar'))->with('event', $createdEvent);
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Error al crear el evento: ' . $e->getMessage());
            }
        }

        return redirect()->route('backend.google.redirect');
    }
}
