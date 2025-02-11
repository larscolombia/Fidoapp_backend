<?php

namespace App\Helpers;

use App\Models\Coin;
use App\Models\User;
use Modules\Tax\Models\Tax;
use Modules\Service\Models\Service;
use Modules\Service\Models\ServiceDuration;
use Modules\Service\Http\Controllers\Backend\API\ServiceController;
use Modules\Booking\Http\Controllers\Backend\API\BookingsController;
use Modules\Service\Http\Controllers\Backend\API\ServiceDurationController;
use Illuminate\Support\Str;

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

    public static function generateSlugInUser($user)
    {
        $baseSlug = Str::slug($user->first_name . ' ' . $user->last_name);

        // Asegurarse de que el slug sea único
        $slug = $baseSlug;
        $counter = 1;
        while (User::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // Asignar y guardar el slug al usuario
        $user->slug = $slug;
        $user->save();
    }
}
