<?php

namespace App\Helpers;

use Modules\Tax\Models\Tax;

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
}
