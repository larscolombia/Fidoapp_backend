<?php

namespace App\Helpers;

use App\Models\Coin;
use Modules\Tax\Models\Tax;
use Modules\Service\Models\Service;
use Modules\Service\Models\ServiceDuration;
use Modules\Service\Http\Controllers\Backend\API\ServiceController;
use Modules\Booking\Http\Controllers\Backend\API\BookingsController;
use Modules\Service\Http\Controllers\Backend\API\ServiceDurationController;

class Functions
{
    public static function calculateTotalWithTax(float $amount): float
    {
        $taxAmount = 0.0;

        // Obtener todos los impuestos desde el modelo Tax
        $taxes = Tax::where('status', 1)->get();

        foreach ($taxes as $tax) {
            if ($tax->type === 'fixed') {
                $taxAmount += $tax->value;
            } elseif ($tax->type === 'percentage') {
                $taxAmount += ($amount * ($tax->value / 100));
            }
        }

        // Calcular el monto total
        $totalAmount = $amount + $taxAmount;
        return round($totalAmount, 2);
    }

    // public function bookingCreate($validatedData, $event)
    // {
    //     $bookingController = new BookingsController();
    //     $coin = Coin::first();
    //     // Asignar el tipo de reserva basado en el enum
    //     $bookingType = match ($event->tipo) {
    //         'medico' => 'veterinary',
    //         'entrenamiento' => 'training'
    //     };

    //     $service = null;
    //     $serviceDuration = null;
    //     $training = null;
    //     $serviceAmount = [
    //         'amount' => 0,
    //         'tax' => 0,
    //         'total_amount' => 0
    //     ];
    //     if ($event->service_id && $bookingType == 'veterinary') {
    //         $service = Service::find($event->service_id);
    //         $serviceController = new ServiceController();
    //         $response = $serviceController->servicePrice($request);
    //         $responseData = json_decode($response->getContent(), true);
    //         if ($responseData['status']) {
    //             $serviceAmount['amount'] = round($this->amountWithoutSymbol($responseData['data']['amount'], $coin->symbol), 2);
    //             $serviceAmount['tax'] = round($this->amountWithoutSymbol($responseData['data']['tax'], $coin->symbol), 2);
    //             $serviceAmount['total_amount'] = round($this->amountWithoutSymbol($responseData['data']['total_amount'], $coin->symbol), 2);
    //         }
    //     }
    //     if ($event->duration_id && $bookingType == 'training') {
    //         $serviceDuration = ServiceDuration::find($event->duration_id);
    //         $serviceDurationController = new ServiceDurationController();
    //         $response = $serviceDurationController->duration_price($request);
    //         // Asegurarse de que la respuesta sea válida y extraer los datos
    //         $responseData = json_decode($response->getContent(), true);
    //         if ($responseData['status']) {
    //             $serviceAmount['amount'] = round($this->amountWithoutSymbol($responseData['data']['amount'], $coin->symbol), 2);
    //             $serviceAmount['tax'] = round($this->amountWithoutSymbol($responseData['data']['tax'], $coin->symbol), 2);
    //             $serviceAmount['total_amount'] = round($this->amountWithoutSymbol($responseData['data']['total_amount'], $coin->symbol), 2);
    //         }
    //     }
    //     if ($event->training_id && $bookingType == 'training') {
    //         $training = Service::find($event->training_id);
    //     }
    //     // Crear el array de datos de la reserva
    //     $bookingData = [
    //         'booking_type' => $bookingType,
    //         'date_time' => $validatedData['date'],
    //         'user_id' => $event->user_id,
    //         'total_amount' => isset($serviceAmount) ? round($serviceAmount['total_amount'], 2) : 0,
    //         'event_id' => $event->id,
    //         'service_amount' => isset($serviceAmount) ? round($serviceAmount['amount'], 2) : 0,
    //         'price' => isset($serviceAmount) ? round($serviceAmount['amount'], 2) : 0,
    //         'system_service_id' => !is_null($service) ? $event->service_id : null,
    //         'service_name' => !is_null($service) ? $service->name : null,
    //         'reason' => $event->description,
    //         'service_id' => !is_null($service) ? $service->id : null,
    //         'duration' => $bookingType == 'training' ? $serviceDuration->id : 0,
    //         'start_video_link' => null,
    //         'join_video_link' => null,
    //         'employee_id' => $request->employee_id,
    //         'training_id' => !is_null($training) ? $training->id : null,
    //         'status' => 'pending'
    //     ];

    //     // Agregar los datos de la reserva a la solicitud
    //     $request->merge($bookingData);

    //     // Llamar al método store del controlador
    //     return $bookingController->store($request);
    // }
    private function amountWithoutSymbol($amount, $symbol)
    {
        $amountFormat = str_replace($symbol, '', $amount);
        return $amountFormat;
    }
}
