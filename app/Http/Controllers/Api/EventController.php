<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\EventDetail;
use Illuminate\Http\Request;
use Modules\Event\Models\Event;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Event\StoreRequest;
use App\Http\Requests\Api\Event\UpdateRequest;

class EventController extends Controller
{
    /**
     * Obtener todos los eventos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $events = Event::all();

        return response()->json([
            'success' => true,
            'message' => 'Eventos recuperados exitosamente',
            'data' => $events,
        ]);
    }

    public function getEventsByUser($user_id)
    {
        $events = Event::where('user_id', $user_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Eventos recuperados exitosamente',
            'data' => $events
        ]);
    }

    public function store(StoreRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $eventTime = $request->input('event_time')
                ? Carbon::createFromFormat('H:i', $request->input('event_time'))->format('H:i:s') : null;
            $event = Event::create([
                'name'        => $request->input('name'),
                'date'        => $request->input('date'),
                'end_date'    => $request->input('end_date'),
                'event_time'  => $eventTime,
                'slug'        => $request->input('slug'),
                'user_id'     => $request->input('user_id'),
                'description' => $request->input('description'),
                'location'    => $request->input('location'),
                'tipo'        => $request->input('tipo'),
                'status'      => $request->input('status'),
            ]);

            $detailEvent = EventDetail::create([
                'event_id' => $event->id,
                'pet_id' => $request->input('pet_id'),
                'owner_id' => $request->input('owner_id'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Evento creado exitosamente',
                'data'    =>  [
                    'event'       => $event,
                    'detail_event' => $detailEvent,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el evento: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        try {
            $event = Event::findOrFail($id);
            $detailEvent = EventDetail::where('event_id', $event->id)->firstOrFail();
            $eventTime = $request->input('event_time')
                ? Carbon::createFromFormat('H:i', $request->input('event_time'))->format('H:i:s') : null;
            $event->update([
                'name'        => $request->input('name', $event->name),
                'date'        => $request->input('date', $event->date),
                'end_date'    => $request->input('end_date', $event->end_date),
                'event_time'  => !is_null($eventTime) ? $eventTime : $event->event_time,
                'slug'        => $request->input('slug', $event->slug),
                'user_id'     => $request->input('user_id', $event->user_id),
                'description' => $request->input('description', $event->description),
                'location'    => $request->input('location', $event->location),
                'tipo'        => $request->input('tipo', $event->tipo),
                'status'      => $request->input('status', $event->status),
            ]);
            $detailEvent->update([
                'pet_id'   => $request->input('pet_id', $detailEvent->pet_id),
                'owner_id' => $request->input('owner_id', $detailEvent->owner_id),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Evento actualizado exitosamente',
                'data' =>  [
                    'event'       => $event,
                    'detail_event' => $detailEvent,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el evento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostrar un evento específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Evento recuperado exitosamente',
            'data' => $event,
        ]);
    }

    public function acceptOrRejectEvent(Request $request)
    {
        try {
            $data = $request->validate([
                'confirm' => 'required|boolean',
                'user_id' => 'required|exists:users,id',
                'event_id' => 'required|exists:events,id'
            ]);

            $eventDetail = EventDetail::where('event_id', $data['event_id'])
                ->where('owner_id', $data['user_id'])
                ->where('confirm', 'P')
                ->first();

            if ($eventDetail) {
                $eventDetail->update([
                    'confirm' => $data['confirm'] ? 'A' : 'R'
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Evento actualizado exitosamente',
                    'data' => [
                        'event' => $eventDetail->event,
                        'detail_event' => $eventDetail,
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se encontró el detalle del evento o ya ha sido actualizado.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un evento específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evento eliminado exitosamente',
        ]);
    }
}
