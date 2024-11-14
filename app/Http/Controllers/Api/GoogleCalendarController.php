<?php

namespace App\Http\Controllers\Api;

use App\Models\CalendarEvent;
use Carbon\Carbon;
use Modules\Event\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Illuminate\Support\Facades\Log;

class GoogleCalendarController extends Controller
{
    protected $client;

    public function createEvent(Request $request)
    {
        $token = $request->session()->get('google_calendar_token');

        if (!$token) {
            return response()->json(['error' => 'No Google Calendar token found.'], 401);
        }

        $this->client->setAccessToken($token);

        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $this->client->getRefreshToken();
            $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            $request->session()->put('google_calendar_token', $this->client->getAccessToken());
        }

        $service = new Google_Service_Calendar($this->client);

        $event = Event::find($request->input('id_event'));

        if (!$event) {
            return response()->json(['error' => 'Event not found.'], 404);
        }

        // Convertir las fechas al formato ISO 8601 con Carbon y agregar la hora del evento
        $date_event = $event->date;
        $end_date_event = $event->end_date;
        $event_time = $event->event_time; // Captura la hora del evento

        if (empty($date_event) || empty($end_date_event) || empty($event_time)) {
            return response()->json(['error' => 'Fecha o hora no proporcionadas o incorrectas.'], 400);
        }

        // Concatenar la fecha con la hora y formatear a ISO 8601 para Google Calendar
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date_event . ' ' . $event_time, 'America/Bogota')
        ->format(\DateTime::ATOM);
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $end_date_event . ' ' . $event_time, 'America/Bogota')
        ->format(\DateTime::ATOM);

        // Crear un nuevo evento de Google Calendar con fecha y hora
        $googleEvent = new Google_Service_Calendar_Event([
            'summary' => $event->name,
            'location' => $event->location,
            'description' => $event->description,
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
            $createdEvent = $service->events->insert($calendarId, $googleEvent);

            if ($createdEvent) {
                CalendarEvent::create([
                    'user_id' => auth()->id(),
                    'event_id' => $request->input('id_event'),
                    'url' => $createdEvent->htmlLink,
                    'proveedor' => 'google',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => __('event.Event added to Google Calendar'),
                    'event' => $createdEvent
                ], 201);
            }

            return response()->json(['error' => 'Failed to create Google Calendar event.'], 500);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al crear el evento: ' . $e->getMessage()], 500);
        }
    }

}
