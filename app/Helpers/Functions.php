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

    public static function getVideoDuration($videoPath)
    {
        $getID3 = new \getID3;
        $file = $getID3->analyze($videoPath);
        $duration = date('H:i:s.v', $file['playtime_seconds']);
        $parts = explode(".", $duration);
        $durationFormat = $parts[0];
        return $durationFormat;
    }

    //@deprecated
    // public static function getDurationText($durationString)
    // {
    //     // Verificar si la cadena tiene el formato HH:MM:SS
    //     $pattern = '/^(\d{2}):(\d{2}):(\d{2})$/';
    //     if (!preg_match($pattern, $durationString, $matches)) {
    //         return '0 segundos'; // O cualquier otro valor que desees retornar si el formato es incorrecto
    //     }
    //     // Convertir la cadena "HH:MM:SS" a segundos
    //     list($hours, $minutes, $seconds) = explode(':', $durationString);
    //     $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;

    //     if ($totalSeconds < 60) {
    //         return $totalSeconds . ' segundos';
    //     } else {
    //         $minutes = $totalSeconds / 60;
    //         return number_format($minutes, 2) . ' minutos';
    //     }
    // }

    public static function getDurationText($durationString)
    {
        $pattern = '/^(\d{2}):(\d{2}):(\d{2})$/';
        if (!preg_match($pattern, $durationString, $matches)) {
            return '0 segundos';
        }

        list($hours, $minutes, $seconds) = explode(':', $durationString);
        $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;

        if ($totalSeconds < 60) {
            return $totalSeconds . ' segundos';
        } else {
            $totalMinutes = floor($totalSeconds / 60);
            $remainingSeconds = $totalSeconds % 60;
            // Convertir segundos a fracción decimal de minuto
            $decimalSeconds = $remainingSeconds / 60;
            $minutesDecimal = $totalMinutes + $decimalSeconds;
            return number_format($minutesDecimal, 2) . ' minutos';
        }
    }
}
