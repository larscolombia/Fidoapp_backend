<?php

namespace App\Http\Controllers\Backend;

use App\Models\Coin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CoinController extends Controller
{
    public function index()
    {
         // Buscar el registro existente por ID
        $coin = Coin::first();

        return response()->json($coin, 200);
    }

    public function store(Request $request)
    {
        // Validar los datos entrantes
        $data = $request->validate([
            'symbol' => 'required|string|max:10',
            'minimum_recharge' => 'required|numeric|min:1',
            'conversion_rate' => 'required|numeric',
        ]);

        try {
            // Buscar si ya existe una moneda con el mismo símbolo
            $currency = Coin::where('id', $request->id)
            ->first();

            if ($currency) {
                // Actualizar la moneda existente
                $currency->symbol = $request->symbol;
                $currency->minimum_recharge = $request->minimum_recharge;
                $currency->conversion_rate = $request->conversion_rate;
                $currency->save();
            } else {
                // Crear una nueva moneda
                $currency = Coin::create([
                    'symbol' => $request->symbol,
                    'minimum_recharge' => $request->minimum_recharge,
                    'conversion_rate' => $request->conversion_rate,
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $currency,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
