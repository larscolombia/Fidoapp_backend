<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Coin;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\EventDetail;
use App\Trait\Notification;
use Illuminate\Http\Request;
use Modules\Event\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\Service\Models\Service;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Contract\Messaging;
use Modules\Service\Models\ServiceDuration;
use App\Http\Controllers\CheckoutController;
use App\Http\Requests\Api\Event\StoreRequest;
use App\Http\Requests\Api\Event\UpdateRequest;
use App\Http\Controllers\Api\NotificationPushController;
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
                    return response()->json(['success' => false, 'error' => 'Insufficient balance', 'amount_service' => $checkBalance['amount']], 400);
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
            $bookingId = null;
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
                    $bookingId = $dataArray['data']['id'];
                    $request->merge($booking);
                    $chekcoutController->store($request, $service['total_amount']);
                }
            }
            if (!in_array($request->input('user_id'), $ownerIds)) {
                $ownerIds[] = $request->input('user_id');
            }
           // $titleEvent = $event->name;
            $titleNotificationEvent = 'Nuevo Evento';
            if($request->input('tipo') === 'medico'){
                $titleNotificationEvent = 'Nuevo Evento Médico';
            }
            if($request->input('tipo') === 'entrenamiento'){
                $titleNotificationEvent = 'Nuevo Evento Entrenamiento';
            }
            // Notificación
            foreach($ownerIds as $ownerId){
                $this->generateNotification($titleNotificationEvent,$event->description,$ownerId);
            }
            $this->sendNotification($request->input('user_id'),$request->input('tipo'), $titleNotificationEvent, $event, $ownerIds, $event->description, $bookingId);

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
            $bookingId = null;
            if (!is_null($request->input('owner_id'))) {
                // Eliminar detalles existentes
                $pet_id = !is_null($event->detailEvent->first()) ? $event->detailEvent->first()->pet_id : null;
                if ($detailEvent) {
                    EventDetail::where('event_id', $event->id)->delete();
                }
                // Asegurarse de que ownerIds sea un array
                $ownerIds = $request->input('owner_id', []);
                // Verificar si $ownerIds no está vacío
                if (isset($ownerIds) && !empty($ownerIds) && is_array($ownerIds)) {
                    foreach ($ownerIds as $ownerId) {
                        EventDetail::firstOrCreate([
                            'event_id' => $event->id,
                            'pet_id'   => $request->input('pet_id', $pet_id),
                            'owner_id' => $ownerId,
                        ]);
                    }
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
                    $bookingId = $existBooking->id;
                }
            } else {
                if ($request->has('pet_id')) {
                    EventDetail::where('event_id', $event->id)->update(['pet_id' => $request->input('pet_id')]);
                }
            }
            $ownerIds = [];
            if (!is_null($request->input('owner_id'))) {
                $ownerIds = $request->input('owner_id');
            }
             // Agregar el user_id a la lista de ownerIds
             if (!in_array($event->user_id, $ownerIds)) {
                $ownerIds[] = $event->user_id;
            }
            $titleNotificationEvent = 'Actualización del Evento';
            if($request->input('tipo') === 'medico'){
                $titleNotificationEvent = 'Actualización del Evento Médico';
            }
            if($request->input('tipo') === 'entrenamiento'){
                $titleNotificationEvent = 'Actualización del Evento Entrenamiento';
            }
            foreach($ownerIds as $ownerId){
                $this->generateNotification($titleNotificationEvent,__('messages.event_update'),$ownerId);
            }
            $this->sendNotification($event->user_id,$event->tipo, $titleNotificationEvent, $event, $ownerIds, __('messages.event_update'), $bookingId);

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
                $user = User::find($data['user_id']);
                //mensaje para la notificacion
                $message = null;
                //actualizar reserva
                $booking = Booking::where('event_id', $data['event_id'])->where('employee_id', $data['user_id'])->where('status', 'pending')->first();
                if ($booking) {
                    $booking->status = $data['confirm'] ? 'confirmed' : 'rejected';
                    $booking->save();
                    if (!$data['confirm']) {
                        //buscamos la operacion del cliente
                        $setting = Setting::where('name', 'str_payment_method')->first();
                        $payment = Payment::create([
                            'amount' => $booking->total_amount,
                            'description' =>  __('booking.refund_for_event') . $eventDetail->event->name,
                            'user_id' => $booking->user_id,
                            'payment_method_id' => !is_null($setting) ? $setting->id : 19,
                            'status' => false
                        ]);
                        if ($payment) {
                            //actualizar wallet del usuario
                            $wallet = Wallet::where('user_id', $booking->user_id)->first();
                            if ($wallet) {
                                $wallet->balance = $wallet->balance + $payment->amount;
                                $wallet->save();
                            }
                        }
                        $message = __('messages.reject_event');
                    } else {
                        $message = __('messages.accept_event');
                    }

                    //reemplazando campos dinamicos
                    $message = str_replace(':profesional', __('event.employee'), $message);
                    $message = str_replace(':nombre', $user->full_name, $message);
                    $message = str_replace(':evento', $eventDetail->event->name, $message);
                } else {
                    if (!$data['confirm']) {
                        $message = __('messages.reject_event');
                    } else {
                        $message = __('messages.accept_event');
                    }

                    //reemplazando campos dinamicos
                    $message = str_replace(':profesional', __('event.user'), $message);
                    $message = str_replace(':nombre', $user->full_name, $message);
                    $message = str_replace(':evento', $eventDetail->event->name, $message);
                }
                $event = Event::find($data['event_id']);
                // Obtener todos los owner_id relacionados con el evento
                $ownerIds = EventDetail::where('event_id', $data['event_id'])->pluck('owner_id')->toArray();

                // Agregar el user_id a la lista de ownerIds
                if (!in_array($data['user_id'], $ownerIds)) {
                    $ownerIds[] = $data['user_id'];
                }
                if (!in_array($event->user_id, $ownerIds)) {
                    $ownerIds[] = $event->user_id;
                }

                //enviando notificacion
                $this->generateNotification($event->name,$message,$data['user_id']);
                $this->sendNotification($data['user_id'],$event->tipo, $event->name, $event, $ownerIds, $message);
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
        $coin = Coin::first();
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
                $serviceAmount['amount'] = round($this->amountWithoutSymbol($responseData['data']['amount'],$coin->symbol), 2);
                $serviceAmount['tax'] = round($this->amountWithoutSymbol($responseData['data']['tax'],$coin->symbol), 2);
                $serviceAmount['total_amount'] = round($this->amountWithoutSymbol($responseData['data']['total_amount'],$coin->symbol), 2);
            }
        }
        if ($request->input('duration_id') && $bookingType == 'training') {
            $serviceDuration = ServiceDuration::find($request->input('duration_id'));
            $serviceDurationController = new ServiceDurationController();
            $response = $serviceDurationController->duration_price($request);
            // Asegurarse de que la respuesta sea válida y extraer los datos
            $responseData = json_decode($response->getContent(), true);
            if ($responseData['status']) {
                $serviceAmount['amount'] = round($this->amountWithoutSymbol($responseData['data']['amount'],$coin->symbol), 2);
                $serviceAmount['tax'] = round($this->amountWithoutSymbol($responseData['data']['tax'],$coin->symbol), 2);
                $serviceAmount['total_amount'] = round($this->amountWithoutSymbol($responseData['data']['total_amount'],$coin->symbol), 2);
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

    private function amountWithoutSymbol($amount,$symbol){
        $amountFormat = str_replace($symbol,'',$amount);
        return $amountFormat;
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
        $coin = Coin::first();
        if ($request->input('service_id') && $bookingType == 'veterinary') {
            $service = Service::find($request->input('service_id'));
            $serviceController = new ServiceController();
            $response = $serviceController->servicePrice($request);
            $responseData = json_decode($response->getContent(), true);
            if ($responseData['status']) {
                $serviceAmount['amount'] = round($this->amountWithoutSymbol($responseData['data']['amount'],$coin->symbol), 2);
                $serviceAmount['tax'] = round($this->amountWithoutSymbol($responseData['data']['tax'],$coin->symbol), 2);
                $serviceAmount['total_amount'] = round($this->amountWithoutSymbol($responseData['data']['total_amount'],$coin->symbol), 2);
            }
        }
        if ($request->input('duration_id') && $bookingType == 'training') {
            $serviceDuration = ServiceDuration::find($request->input('duration_id'));
            $serviceDurationController = new ServiceDurationController();
            $response = $serviceDurationController->duration_price($request);
            // Asegurarse de que la respuesta sea válida y extraer los datos
            $responseData = json_decode($response->getContent(), true);
            if ($responseData['status']) {
                $serviceAmount['amount'] = round($this->amountWithoutSymbol($responseData['data']['amount'],$coin->symbol), 2);
                $serviceAmount['tax'] = round($this->amountWithoutSymbol($responseData['data']['tax'],$coin->symbol), 2);
                $serviceAmount['total_amount'] = round($this->amountWithoutSymbol($responseData['data']['total_amount'],$coin->symbol), 2);
            }
        }

        return $serviceAmount;
    }

    private function generateNotification($title,$description,$userId){
        // Obtén el token del dispositivo del usuario específico
        $user = User::where('id', $userId)->whereNotNull('device_token')->first();
        if ($user) {
           $pushNotificationController = new NotificationPushController(app(Messaging::class));
           $pushNotificationController->sendNotification($title, $description, $user->device_token);
       }
   }
}
