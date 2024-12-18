<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\EventDetail;
use App\Trait\Notification;
use Illuminate\Http\Request;
use Modules\Event\Models\Event;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Event\StoreRequest;
use App\Http\Requests\Api\Event\UpdateRequest;


class EventController extends Controller
{
    use Notification;
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
        $data = $events->map(function ($event) {
            $owners = [];

            foreach ($event->detailEvent as $detail) {
                if ($detail->owner_id) {
                    // Buscar el usuario por ID
                    $user = User::find($detail->owner_id);
                    if ($user) {
                        $avatar = !is_null($user->avatar) && !empty($user->avatar) ? asset($user->avatar) : null;
                        // Agregar el id, correo electrónico y avatar al array
                        $owners[] = ['id' => $user->id, 'email' => $user->email, 'avatar' => $avatar];
                    }
                }
            }

            return [
                'name' => $event->name,
                'tipo' => $event->tipo,
                'date' => $event->date,
                'end_date' => $event->end_date,
                'slug' => $event->slug,
                'user_id' => $event->user_id,
                'user_email' => $event->user ? $event->user->email : null,
                'description' => $event->description,
                'location' => $event->location,
                'status' => $event->status,
                'pet_id' => $event->detailEvent->isNotEmpty() ? $event->detailEvent->first()->pet_id : null,
                'owners' => $owners
            ];
        });
        return response()->json([
            'success' => true,
            'message' => 'Eventos recuperados exitosamente',
            'data' => $data
        ]);
    }

    public function store(StoreRequest $request)
    {
        try {
            $validatedData = $request->validated();
            try {
                $validatedData['date'] = Carbon::createFromFormat('Y-m-d', $validatedData['date'])->format('Y-m-d');
                $validatedData['end_date'] = Carbon::createFromFormat('Y-m-d', $validatedData['end_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $validatedData['date'] = Carbon::now()->format('Y-m-d');
                $validatedData['end_date'] = Carbon::now()->format('Y-m-d');
            }
            $eventTime = $request->input('event_time')
                ? Carbon::createFromFormat('H:i', $request->input('event_time'))->format('H:i:s') : null;

            if (!file_exists(public_path('images/event'))) {
                mkdir(public_path('images/event'), 0755, true);
            }
            $data['image'] = null;
             // Manejar la imagen si se proporciona
             if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalName();
                $image->move(public_path('images/event'), $imageName);
                $imagePath = 'images/event/' . $imageName;
                $data['image'] = $imagePath;
            }
            $event = Event::create([
                'name'        => $request->input('name'),
                'date'        => $validatedData['date'],
                'end_date'    => $validatedData['end_date'],
                'event_time'  => $eventTime,
                'slug'        => $request->input('slug'),
                'user_id'     => $request->input('user_id'),
                'description' => $request->input('description'),
                'location'    => $request->input('location'),
                'tipo'        => $request->input('tipo'),
                'status'      => $request->input('status'),
                'image'       => $data['image']
            ]);

            $ownerIds = $request->input('owner_id');
            foreach ($ownerIds as $ownerId) {
                EventDetail::create([
                    'event_id' => $event->id,
                    'pet_id'   => $request->input('pet_id'),
                    'owner_id' => $ownerId,
                ]);
            }
            $this->sendNotification('event', $event, $request->input('owner_id'), $event->description);
            return response()->json([
                'success' => true,
                'message' => 'Evento creado exitosamente',
                'data'    =>  [
                    'event'       => $event,
                    'detail_event' => $event->detailEvent,
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
            Log::info($request->all());
            $event = Event::findOrFail($id);
            $validatedData = $request->validated();
            $detailEvent = EventDetail::where('event_id', $event->id)->firstOrFail();
            try {
                $validatedData['date'] = Carbon::createFromFormat('Y-m-d', $validatedData['date'])->format('Y-m-d');
                $validatedData['end_date'] = Carbon::createFromFormat('Y-m-d', $validatedData['end_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $validatedData['date'] = null;
                $validatedData['end_date'] = null;
            }
            $eventTime = $request->input('event_time')
                ? Carbon::createFromFormat('H:i', $request->input('event_time'))->format('H:i:s') : null;

             // Manejar la imagen si se proporciona
             if ($request->hasFile('image')) {
                // Eliminar la imagen anterior si existe
                if ($event->image && file_exists(public_path($event->image))) {
                    unlink(public_path($event->image)); // Elimina la imagen anterior
                }
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalName();
                $image->move(public_path('images/event'), $imageName);
                $imagePath = 'images/event/' . $imageName;
                $data['image'] = $imagePath;
            }else{
                $data['image'] = $event->image;
            }
            $event->update([
                'name'        => $request->input('name', $event->name),
                'date'        => !is_null($validatedData['date']) ? $validatedData['date'] : $event->date,
                'end_date'    => !is_null($validatedData['end_date']) ? $validatedData['end_date'] : $event->end_date,
                'event_time'  => !is_null($eventTime) ? $eventTime : $event->event_time,
                'description' => $request->input('description', $event->description),
                'location'    => $request->input('location', $event->location),
                'tipo'        => $request->input('tipo', $event->tipo),
                'status'      => $request->input('status', $event->status),
                'image'       => $data['image'],
            ]);
            if ($request->has('owner_id')) {
                // Eliminar detalles existentes
                EventDetail::where('event_id', $event->id)->delete();

                // Crear nuevos detalles del evento
                $ownerIds = $request->input('owner_id');
                foreach ($ownerIds as $ownerId) {
                    EventDetail::create([
                        'event_id' => $event->id,
                        'pet_id'   => $request->input('pet_id',$event->detailEvent->first()->pet_id),
                        'owner_id' => $ownerId,
                    ]);
                }
            }else{
                if($request->has('pet_id')){
                    EventDetail::where('event_id', $event->id)->update(['pet_id' => $request->input('pet_id')]);
                }
            }


            $this->sendNotification('event', $event, $request->input('owner_id'), $event->description);
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
        $owners = [];
        foreach ($event->detailEvent as $detail) {
            if ($detail->owner_id) {
                // Buscar el usuario por ID
                $user = User::find($detail->owner_id);
                if ($user) {
                    $avatar = !is_null($user->avatar) && !empty($user->avatar) ? asset($user->avatar) : null;
                    // Agregar el id, correo electrónico y avatar al array
                    $owners[] = ['id'=> $user->id, 'email' => $user->email, 'avatar' => $avatar];
                }
            }
        }
        $data =  [
            'name' => $event->name,
            'tipo'        => $event->tipo,
            'date'        => $event->date,
            'end_date'    => $event->end_date,
            'slug'        => $event->slug,
            'user_id'     => $event->user_id,
            'user_email'  => $event->user->email,
            'description' => $event->description,
            'location'    => $event->location,
            'status'      => $event->status,
            'pet_id'      => $event->detailEvent->isNotEmpty() ? $event->detailEvent->first()->pet_id : null,
            'owners'   => $owners

        ];
        return response()->json([
            'success' => true,
            'message' => 'Evento recuperado exitosamente',
            'data'    =>$data,
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

    public function getPetByEvent(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);
            $eventDetails = EventDetail::where('owner_id', $data['user_id'])
                ->where('confirm', 'A')
                ->get();
            $results = $eventDetails->map(function ($eventDetail) {
                return [
                    'event_id' => $eventDetail->event->id,
                    'event_detail_id' => $eventDetail->id,
                    'event_date' => $eventDetail->event->date,
                    'event_end_date' => $eventDetail->event->end_date,
                    'event_name' => $eventDetail->event->name,
                    'event_time' => $eventDetail->event->event_time,
                    'pet_id' => $eventDetail->pet->id,
                    'pet_name' => $eventDetail->pet->name,
                    'pet_qr_code' => is_null($eventDetail->pet->qr_code) || empty($eventDetail->pet->qr_code) ? null : asset($eventDetail->pet->qr_code),
                    'user_id' => $eventDetail->pet->user_id,
                    'user_full_name' => $eventDetail->pet->user->full_name,
                    'user_avatar' => is_null($eventDetail->pet->user->avatar) || empty($eventDetail->pet->user->avatar) ? null : asset($eventDetail->pet->user->avatar)
                ];
            });
            return response()->json([
                'success' => true,
                'data' => $results,
            ]);
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
