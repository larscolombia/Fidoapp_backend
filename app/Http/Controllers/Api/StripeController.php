<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Payment;
use Illuminate\Support\Str;
use App\Models\CachePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class StripeController extends Controller
{
    public $sk_key = "sk_test_51I3R8hFWfM6dcSbz41CTp614CT2MCUOvFKyaY9XHpdxov8nn34SpTq59hoMOLjeMgiXTsfyi9PxgskQoW7UTItng00KWw2a7Ye";
    public $url_stripe = 'https://api.stripe.com/v1';

    public function enpoint($key)
    {
        $data = [
            'price' => "{$this->url_stripe}/prices",
            'producto' =>  "{$this->url_stripe}/products",
            'checkout' => "checkout/sessions"
        ];
        return $data[$key];
    }
    public function createSession(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required',
                'description' => 'required',
                'id_user' => 'required',
                'id_service' => 'required'
            ]);
            // Generar un token único para identificar el request
            $token = Str::random(32); // Genera un token aleatorio de 32 caracteres
            //almacenamos en la cache
            CachePayment::create([
                'metadata' => json_encode($request->all()),
                'token' => $token
            ]);
            //buscamos la clave de stripe
            $appSetting = DB::table('app_settings')->where('key', 'stripe_secret')->where('key', '!=', null)->first();
            if ($appSetting) {
                $this->sk_key = $appSetting->value;
            } else {
                $this->sk_key = env('SK_STRIPER');
            }
            $priceId = "price_1OjTDdFWfM6dcSbzYPInqFnW";
            $response = Http::withBasicAuth($this->sk_key, '')
                ->asForm() // Asegura que los datos se envíen como application/x-www-form-urlencoded
                ->post('https://api.stripe.com/v1/checkout/sessions', [

                    'line_items[0][price]' =>  $priceId, //'price_1OjTDdFWfM6dcSbzYPInqFnW', // ID del precio
                    'line_items[0][quantity]' => 1, // Cantidad
                    'mode' => 'payment', // Modo de pago
                    'success_url' => route('striper.sucess', ['id_servicio' => $request['id_service'], 'token' => $token]), // URL de redireccionamiento de éxito
                    'cancel_url' => route('striper.cancel', $request['id_service']),
                ]);

            if (isset($response['url'])) {
                return $response['url'];
            } else {
                return response()->json([
                    'success' => 'error',
                    'message' => __('lang.stripe_url_error')
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,

                'message' => $e->getMessage(),
            ]);
        }
        return $response;
    }

    public function success($id_servicio, $token)
    {
        //recuperamos el token de la session para identificar la request
        try {
            $cahce = CachePayment::where('token', $token)->first();
            if ($cahce) {
                $metadata =  json_decode($cahce->metadata, true);
                //buscamos si existe el pago
                Payment::create([
                    'amount' => $metadata['amount'],
                    'description' => $metadata['descripcion'] ?? '',
                    'user_id' => $metadata['id_user'],
                    'id_service' => $metadata['id_service'],
                    'payment_method_id' => 7,
                    'payment_status_id' => 4
                ]);
                return response()->json([
                    'success' => 'success',
                    'message' => __('lang.stripe_success'),
                    'id_servicio' => $id_servicio,
                    'token' => $token
                ]);
            } else {
                return response()->json([
                    'success' => false,

                    'message' => __('lang.stripe_success_error'),
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,

                'message' => $e->getMessage(),
            ]);
        }
    }

    public function cancel($id)
    {
        return response()->json([
            'success' => 'cancel',
            'message' => 'Pago cancelado',
        ]);
    }

    //enpoitn   ['price' ,'producto' ]
    public function getItemsStriper($enpoint, $id_items)
    {
        $appSetting = DB::table('app_settings')->where('key', 'stripe_fpx_secret')->where('key', '!=', null)->first();
        if ($appSetting) {
            $this->sk_key = $appSetting->value;
        }
        $response = Http::withBasicAuth($this->sk_key, '')
            ->get($this->enpoint($enpoint) . "/$id_items");

        if ($response->successful()) {
            // La solicitud fue exitosa, procesa la respuesta
            return $planData = $response->json();
        } else {
            // Maneja errores
            return response()->json(['error' => $response->json()], 500);
        }
    }
}
