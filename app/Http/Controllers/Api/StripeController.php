<?php

namespace App\Http\Controllers\Api;

use Exception;
use Stripe\Stripe;
use App\Models\Coin;
use App\Models\Wallet;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Str;
use App\Models\CachePayment;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Trait\Notification;

class StripeController extends Controller
{
    use Notification;
    private $sk_key, $product;
    private $coin;
    public function __construct()
    {
        // Obtener la clave secreta de Stripe
        $setting = Setting::where('name', 'stripe_secretkey')->first();
        $this->sk_key = $setting->val ?? null;
        // Obtener el primer registro de Coin
        $coin = Coin::first();
        $this->coin = $coin;

        // Asignar el precio del producto basado en la moneda
        $this->product = $coin->coinPrice ?? null;
    }


    public function checkout(Request $request)
    {
        // Validar los datos de entrada
        $min = $this->coin->minimum_recharge;
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'quantity' => ['required', 'string', 'min:' . $min]
        ]);

        // Configura la clave secreta de Stripe
        $this->setStripeApiKey($this->sk_key);

        // ID del precio del producto que deseas vender
        $priceId = $this->product->stripe_price_id;
        $amount = $this->coin->conversion_rate * $data['quantity'];
        try {
            // Crea una sesión de checkout
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => $data['quantity'],
                ]],
                'mode' => 'payment',
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel'),
                'metadata' => [
                    'amount' => $amount,
                    'descripcion' => "Recarga de FidoCoin",
                    'id_user' => $data['user_id'],
                    'id_service' => 19
                ],
            ]);

            // Retorna la URL de la sesión de checkout como respuesta JSON
            return response()->json([
                'success' => true,
                'url' => $session->url,
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores: registrar el error y retornar un mensaje de error en formato JSON
            \Log::error('Error al crear la sesión de checkout: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al procesar su pago. Por favor, inténtelo de nuevo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function success(Request $request)
    {
        $this->setStripeApiKey($this->sk_key);
        // Aquí puedes manejar la lógica después del pago exitoso
        $sessionId = $request->get('session_id');

        try {
            // Recuperar la sesión de Stripe para verificar el estado del pago
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $existPayment = Payment::where('stripe_session_id', $sessionId)->first();
                if (!$existPayment) {
                    // Extraer los metadatos de la sesión
                    $metadata = $session->metadata;
                    //buscamos el id del metodo de pago
                    $setting = Setting::where('name', 'str_payment_method')->first();
                    $payment = Payment::create([
                        'amount' => $metadata['amount'],
                        'description' => $metadata['descripcion'] ?? '',
                        'user_id' => $metadata['id_user'],
                        'id_service' => $metadata['id_service'],
                        'payment_method_id' => !is_null($setting) ? $setting->id : 19,
                        'stripe_session_id' =>  $sessionId
                    ]);
                    //actualizar wallet del usuario
                    $wallet = Wallet::where('user_id', $metadata['id_user'])->first();
                    if ($wallet) {
                        $wallet->balance = $wallet->balance + $metadata['amount'];
                        $wallet->save();
                    }
                    $message = __('messages.success_buy_fidocoins');
                    //enviamos la notificacion
                    $this->sendNotification($metadata['id_user'],'fidocoin', __('coin.buy_fidocoin'), $payment, [$metadata['id_user']], $message);
                    return view('backend.payment.success', [
                        'session' => $session,
                        'payment' => $payment,
                        'message' => 'Recarga hecha con éxito',
                    ]);
                    // return response()->json([
                    //     'status' => 'success',
                    //     'message' => 'El pago ha sido procesado exitosamente.',
                    //     'session' => $session,
                    // ], 200);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'El pago no se completó.',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al recuperar la sesión: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function cancel()
    {
        return response()->json([
            'status' => 'canceled',
            'message' => 'El proceso de pago ha sido cancelado.',
        ], 200);
    }


    private function setStripeApiKey($secretKey)
    {
        Stripe::setApiKey($secretKey);
    }
}
