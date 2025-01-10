<?php

namespace Modules\Service\Http\Controllers\Backend\API;

use App\Models\Coin;
use App\Helpers\Functions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Service\Models\ServiceDuration;
use Illuminate\Contracts\Support\Renderable;
use Modules\Service\Transformers\ServiceDurationResource;

class ServiceDurationController extends Controller
{
    public function durationList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $serviceduration =  ServiceDuration::where('status', 1);

        if ($request->has('search')) {
            $serviceduration->where('type', 'like', "%{$request->search}%");
        }

        if ($request->has('type') && $request->type != '') {
            $serviceduration = $serviceduration->Where('type', $request->type);
        }

        $serviceduration = $serviceduration->paginate($perPage);
        $items = ServiceDurationResource::collection($serviceduration);


        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('service.duration_list'),
        ], 200);
    }

    public function durationListAll(Request $request)
    {

        $serviceduration =  ServiceDuration::where('status', 1);

        if ($request->has('type') && $request->type != '') {
            $serviceduration = $serviceduration->Where('type', $request->type);
        }

        $serviceduration = $serviceduration->get();
        $items = ServiceDurationResource::collection($serviceduration);


        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('service.duration_list'),
        ], 200);
    }

    public function duration_price(Request $request)
    {
        try {
            // Validar la entrada
            $data = $request->validate([
                'duration_id' => ['required', 'exists:service_duration,id'] // Asegurarse de que el ID exista
            ]);

            // Obtener el servicio de duración
            $serviceDuration = ServiceDuration::findOrFail($data['duration_id']); // Utilizar findOrFail para manejar errores
            $coin = Coin::first();
            // Calcular el precio total con impuestos
            $durationPrice = round(Functions::calculateTotalWithTax($serviceDuration->price), 2);
            $tax = round(($durationPrice - $serviceDuration->price), 2);

            // Retornar la respuesta JSON
            return response()->json([
                'data' => [
                    'amount' => round($serviceDuration->price, 2).$coin->symbol,
                    'tax' => $tax.$coin->symbol,
                    'total_amount' => $durationPrice.$coin->symbol
                ],
                'status' => true
            ]);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'error' => 'Ocurrió un error al procesar la solicitud.',
                'message' => $e->getMessage(), // Mensaje de error para depuración (opcional)
                'status' => false
            ], 500); // Código de estado HTTP 500 para errores del servidor
        }
    }
}
