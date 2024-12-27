<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Wallet;
use App\Models\EventDetail;
use App\Trait\Notification;
use Illuminate\Http\Request;
use Modules\Event\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\Service\Models\Service;
use App\Http\Controllers\Controller;
use Modules\Service\Models\ServiceDuration;
use App\Http\Controllers\CheckoutController;
use App\Http\Requests\Api\Event\StoreRequest;
use App\Http\Requests\Api\Event\UpdateRequest;
use Modules\Service\Http\Controllers\Backend\API\ServiceController;
use Modules\Booking\Http\Controllers\Backend\API\BookingsController;
use Modules\Service\Http\Controllers\Backend\API\ServiceDurationController;


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
        $today = \Carbon\Carbon::today();
        $fiveDaysLater = $today->copy()->addDays(5);

        // Obtener eventos filtrando por user_id y el rango de fechas
        $events = Event::where('user_id', $user_id)
            ->whereBetween('updated_at', [$today, $fiveDaysLater])
            ->get();
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
                'id' => $event->id,
                'name' => $event->name,
                'tipo' => $event->tipo,
                'date'        =>  \Carbon\Carbon::parse($event->date)->format('d-m-Y'),
                'end_date'    => \Carbon\Carbon::parse($event->end_date)->format('d-m-Y'),
                'event_time' => \Carbon\Carbon::parse($event->event_time)->format('H:i'),
                'slug' => $event->slug,
                'user_id' => $event->user_id,
                'user_email' => $event->user ? $event->user->email : null,
                'description' => $event->description,
                'location' => $event->location,
                'status' => $event->status,
                'pet_id' => $event->detailEvent->isNotEmpty() ? $event->detailEvent->first()->pet_id : null,
                'owners' => $owners,
                //valores complementarios
                'service_id' => isset($event->booking->employee_veterinary) ? $event->booking->employee_veterinary->service_id : null,
                'category_id' => isset($event->booking->employee_veterinary) && isset($event->booking->employee_veterinary->service) && isset($event->booking->employee_veterinary->service->category) ?
                    $event->booking->employee_veterinary->service->category->id : null,

                'training_id' => isset($event->booking->employee_training) ? $event->booking->employee_training->training_id : null,
                'duration_id' => isset($event->booking->employee_training) ? intval($event->booking->employee_training->duration) : null,
                'image' => isset($event->image) ? asset($event->image) : null

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
        DB::beginTransaction(); // Iniciar la transacción
        $service = null;
        try {
            $validatedData = $request->validated();
            // Verificar si el tipo es "medico" o "entrenamiento"
            if (in_array($request->input('tipo'), ['medico', 'entrenamiento'])) {
                // Asignar el tipo de reserva basado en el enum
                $bookingType = match ($request->input('tipo')) {
                    'medico' => 'veterinary',
                    'entrenamiento' => 'training'
                };
                $service = $this->service($request, $bookingType);
                $checkBalance = $this->checkBalance($request, $service);
                if (!$checkBalance['success']) {
                    return response()->json(['success' => false, 'error' => 'Insufficient balance'], 400);
                }
            }
            try {
                $validatedData['date'] = Carbon::createFromFormat('Y-m-d', $validatedData['date'])->format('Y-m-d');
                $validatedData['end_date'] = Carbon::createFromFormat('Y-m-d', $validatedData['end_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $validatedData['date'] = Carbon::now()->format('Y-m-d');
                $validatedData['end_date'] = Carbon::now()->format('Y-m-d');
            }

            $eventTime = $request->input('event_time')
                ? Carbon::createFromFormat('H:i', $request->input('event_time'))->format('H:i') : null;

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

            // Crear el evento
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

            // Crear los detalles del evento
            $ownerIds = $request->input('owner_id');
            $professionalId = ['employee_id' => null];
            foreach ($ownerIds as $ownerId) {
                EventDetail::create([
                    'event_id' => $event->id,
                    'pet_id'   => $request->input('pet_id'),
                    'owner_id' => $ownerId,
                ]);
                $professionalUser = User::find($ownerId);
                if ($professionalUser->user_type == 'vet' || $professionalUser->user_type == 'trainer') {
                    $professionalId = ['employee_id' => $ownerId];
                }
            }

            // Reserva
            if (in_array($request->input('tipo'), ['medico', 'entrenamiento'])) {
                // Llamar a bookingCreate y manejar su resultado

                $request->merge($professionalId);
                $bookingData = $this->bookingCreate($request, $validatedData, $event);
                $dataArray = json_decode($bookingData->getContent(), true);
                if ($dataArray['status'] === true && $bookingData->getStatusCode() === 200) {
                    $chekcoutController = new CheckoutController();
                    $booking = ['booking_id' => $dataArray['data']['id']];
                    $request->merge($booking);
                    $chekcoutController->store($request, $service['total_amount']);
                }
            }

            $titleEvent = in_array($request->input('tipo'), ['medico', 'entrenamiento']) ? __('event.event') . ' ' . ($request->input('tipo') == 'medico' ? 'médico' : $request->input('tipo')) : __('event.event');
            // Notificación
            $this->sendNotification($titleEvent, $event, $ownerIds, $event->description);

            DB::commit(); // Confirmar la transacción

            return response()->json([
                'success' => true,
                'message' => 'Evento creado exitosamente',
                'data'    =>  [
                    'event'       => $event,
                    'detail_event' => $event->detailEvent,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollback(); // Revertir la transacción en caso de error

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
            $detailEvent = EventDetail::where('event_id', $event->id)->first();
            try {
                $validatedData['date'] = Carbon::createFromFormat('Y-m-d', $validatedData['date'])->format('Y-m-d');
                $validatedData['end_date'] = Carbon::createFromFormat('Y-m-d', $validatedData['end_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $validatedData['date'] = null;
                $validatedData['end_date'] = null;
            }
            $eventTime = $request->input('event_time')
                ? Carbon::createFromFormat('H:i', $request->input('event_time'))->format('H:i') : null;

            // // Manejar la imagen si se proporciona
            // if ($request->hasFile('image')) {
            //     // Eliminar la imagen anterior si existe
            //     if ($event->image && file_exists(public_path($event->image))) {
            //         unlink(public_path($event->image)); // Elimina la imagen anterior
            //     }
            //     $image = $request->file('image');
            //     $imageName = time() . '.' . $image->getClientOriginalName();
            //     $image->move(public_path('images/event'), $imageName);
            //     $imagePath = 'images/event/' . $imageName;
            //     $data['image'] = $imagePath;
            // } else {
            //     $data['image'] = $event->image;
            // }
            $event->update([
                'name'        => $request->input('name', $event->name),
                'date'        => !is_null($validatedData['date']) ? $validatedData['date'] : $event->date,
                'end_date'    => !is_null($validatedData['end_date']) ? $validatedData['end_date'] : $event->end_date,
                'event_time'  => !is_null($eventTime) ? $eventTime : $event->event_time,
                'description' => $request->input('description', $event->description),
                'location'    => $request->input('location', $event->location),
                'status'      => $request->input('status', $event->status),
            ]);
            if ($request->has('owner_id')) {
                // Eliminar detalles existentes
                $pet_id = !is_null($event->detailEvent->first()) ? $event->detailEvent->first()->pet_id : null;
                if ($detailEvent) {
                    $detailEvent->delete();
                }
                // Crear nuevos detalles del evento
                $ownerIds = $request->input('owner_id');
                foreach ($ownerIds as $ownerId) {
                    EventDetail::create([
                        'event_id' => $event->id,
                        'pet_id'   => $request->input('pet_id', $pet_id),
                        'owner_id' => $ownerId,
                    ]);
                }
                //buscamos al profesional en la reserva en base al eventId
                $existBooking = Booking::where('event_id', $event->id)->first();
                if ($existBooking) {
                    //verificamos si esta el profesional en la reserva
                    $existProfessional = EventDetail::where('event_id', $event->id)->where('owner_id', $existBooking->employee_id)->first();
                    if (!$existProfessional) {
                        EventDetail::create([
                            'event_id' => $event->id,
                            'pet_id'   => $request->input('pet_id', $pet_id),
                            'owner_id' => $existBooking->employee_id,
                        ]);
                    }
                }
            } else {
                if ($request->has('pet_id')) {
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
                    $owners[] = ['id' => $user->id, 'email' => $user->email, 'avatar' => $avatar];
                }
            }
        }
        $data =  [
            'id' => $event->id,
            'name' => $event->name,
            'tipo'        => $event->tipo,
            'date'        =>  \Carbon\Carbon::parse($event->date)->format('d-m-Y'),
            'end_date'    => \Carbon\Carbon::parse($event->end_date)->format('d-m-Y'),
            'event_time' => \Carbon\Carbon::parse($event->event_time)->format('H:i'),
            'slug'        => $event->slug,
            'user_id'     => $event->user_id,
            'user_email'  => $event->user->email,
            'description' => $event->description,
            'location'    => $event->location,
            'status'      => $event->status,
            'pet_id'      => $event->detailEvent->isNotEmpty() ? $event->detailEvent->first()->pet_id : null,
            'owners'   => $owners,
            //valores complementarios
            'service_id' => isset($event->booking->employee_veterinary) ? $event->booking->employee_veterinary->service_id : null,
            'category_id' => isset($event->booking->employee_veterinary) && isset($event->booking->employee_veterinary->service) ?
                $event->booking->employee_veterinary->service->category->id : null,

            'training_id' => isset($event->booking->employee_training) ? $event->booking->employee_training->training_id : null,
            'duration_id' => isset($event->booking->employee_training) ? intval($event->booking->employee_training->duration) : null,
            'image' => isset($event->image) ? asset($event->image) : null

        ];
        return response()->json([
            'success' => true,
            'message' => 'Evento recuperado exitosamente',
            'data'    => $data,
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
            //actualizar reserva
            Booking::where('event_id', $data['event_id'])->update(['status' => $data['confirm'] ? 'confirmed' : 'rejected']);

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
            // Definir las fechas de inicio y fin para el rango
            $today = \Carbon\Carbon::today();
            $fiveDaysLater = $today->copy()->addDays(5);
            $eventDetails = EventDetail::where('owner_id', $data['user_id'])
                ->where('confirm', 'A')
                ->orderBy('updated_at', 'desc')
                ->whereBetween('updated_at', [$today, $fiveDaysLater])
                ->get();

            $results = $eventDetails->map(function ($eventDetail) {
                return [
                    'event_id' => $eventDetail->event->id,
                    'event_detail_id' => $eventDetail->id,
                    // Formatear las fechas y la hora
                    'event_date' => \Carbon\Carbon::parse($eventDetail->event->date)->format('d-m-Y'),
                    'event_end_date' => \Carbon\Carbon::parse($eventDetail->event->end_date)->format('d-m-Y'),
                    'event_name' => $eventDetail->event->name,
                    'event_time' => \Carbon\Carbon::parse($eventDetail->event->event_time)->format('H:i'),
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

    //reserva
    private function bookingCreate($request, $validatedData, $event)
    {
        $bookingController = new BookingsController();

        // Asignar el tipo de reserva basado en el enum
        $bookingType = match ($request->input('tipo')) {
            'medico' => 'veterinary',
            'entrenamiento' => 'training'
        };

        $service = null;
        $serviceDuration = null;
        $training = null;
        $serviceAmount = [
            'amount' => 0,
            'tax' => 0,
            'total_amount' => 0
        ];
        if ($request->input('service_id') && $bookingType == 'veterinary') {
            $service = Service::find($request->input('service_id'));
            $serviceController = new ServiceController();
            $response = $serviceController->servicePrice($request);
            $responseData = json_decode($response->getContent(), true);
            if ($responseData['status']) {
                $serviceAmount['amount'] = round($responseData['data']['amount'], 2);
                $serviceAmount['tax'] = round($responseData['data']['tax'], 2);
                $serviceAmount['total_amount'] = round($responseData['data']['total_amount'], 2);
            }
        }
        if ($request->input('duration_id') && $bookingType == 'training') {
            $serviceDuration = ServiceDuration::find($request->input('duration_id'));
            $serviceDurationController = new ServiceDurationController();
            $response = $serviceDurationController->duration_price($request);
            // Asegurarse de que la respuesta sea válida y extraer los datos
            $responseData = json_decode($response->getContent(), true);
            if ($responseData['status']) {
                $serviceAmount['amount'] = round($responseData['data']['amount'], 2);
                $serviceAmount['tax'] = round($responseData['data']['tax'], 2);
                $serviceAmount['total_amount'] = round($responseData['data']['total_amount'], 2);
            }
        }
        if ($request->input('training_id') && $bookingType == 'training') {
            $training = Service::find($request->input('training_id'));
        }
        // Crear el array de datos de la reserva
        $bookingData = [
            'booking_type' => $bookingType,
            'date_time' => $validatedData['date'],
            'user_id' => $request->input('user_id'),
            'total_amount' => isset($serviceAmount) ? round($serviceAmount['total_amount'], 2) : 0,
            'event_id' => $event->id,
            'service_amount' => isset($serviceAmount) ? round($serviceAmount['amount'], 2) : 0,
            'price' => isset($serviceAmount) ? round($serviceAmount['amount'], 2) : 0,
            'system_service_id' => !is_null($service) ? $request->input('service_id') : null,
            'service_name' => !is_null($service) ? $service->name : null,
            'reason' => $request->input('description'),
            'service_id' => !is_null($service) ? $service->id : null,
            'duration' => $bookingType == 'training' ? $serviceDuration->id : 0,
            'start_video_link' => null,
            'join_video_link' => null,
            'training_id' => !is_null($training) ? $training->id : null,
            'status' => 'pending'
        ];

        // Agregar los datos de la reserva a la solicitud
        $request->merge($bookingData);

        // Llamar al método store del controlador
        return $bookingController->store($request);
    }

    private function checkBalance($request, $service)
    {
        $amount = $service['total_amount'];
        $chekcoutController = new CheckoutController();
        $user = User::find($request->input('user_id'));
        $wallet = Wallet::where('user_id', $user->id)->first();
        $checkBalance = $chekcoutController->checkBalance($wallet, $amount);
        return $checkBalance;
    }

    private function service($request, $bookingType)
    {
        $serviceAmount = [
            'amount' => 0,
            'tax' => 0,
            'total_amount' => 0
        ];
        if ($request->input('service_id') && $bookingType == 'veterinary') {
            $service = Service::find($request->input('service_id'));
            $serviceController = new ServiceController();
            $response = $serviceController->servicePrice($request);
            $responseData = json_decode($response->getContent(), true);
            if ($responseData['status']) {
                $serviceAmount['amount'] = round($responseData['data']['amount'], 2);
                $serviceAmount['tax'] = round($responseData['data']['tax'], 2);
                $serviceAmount['total_amount'] = round($responseData['data']['total_amount'], 2);
            }
        }
        if ($request->input('duration_id') && $bookingType == 'training') {
            $serviceDuration = ServiceDuration::find($request->input('duration_id'));
            $serviceDurationController = new ServiceDurationController();
            $response = $serviceDurationController->duration_price($request);
            // Asegurarse de que la respuesta sea válida y extraer los datos
            $responseData = json_decode($response->getContent(), true);
            if ($responseData['status']) {
                $serviceAmount['amount'] = round($responseData['data']['amount'], 2);
                $serviceAmount['tax'] = round($responseData['data']['tax'], 2);
                $serviceAmount['total_amount'] = round($responseData['data']['total_amount'], 2);
            }
        }

        return $serviceAmount;
    }
}
