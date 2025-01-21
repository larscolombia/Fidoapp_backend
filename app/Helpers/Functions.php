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

    private function amountWithoutSymbol($amount, $symbol)
    {
        $amountFormat = str_replace($symbol, '', $amount);
        return $amountFormat;
    }
}
