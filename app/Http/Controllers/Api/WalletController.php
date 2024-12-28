<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\EBook;
use App\Models\Wallet;
use App\Models\Payment;
use App\Models\Checkout;
use Illuminate\Http\Request;
use App\Models\CursoPlataforma;
use Modules\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Validar la solicitud
            $data = $request->validate([
                'user_id' => ['required', 'exists:users,id']
            ]);


            // Obtener la billetera del usuario
            $wallet = Wallet::where('user_id', $data['user_id'])->first();

            // Retornar respuesta exitosa
            return response()->json(['success' => true, 'data' => $wallet]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar errores de validación
            return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            // Manejar caso en que el usuario no se encuentra
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            // Manejar cualquier otro tipo de excepción
            return response()->json(['success' => false, 'message' => 'Error inesperado', 'error' => $e->getMessage()], 500);
        }
    }


    public function deposit(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:0']);

        $wallet = auth()->user()->wallet;
        $wallet->balance += $request->amount;
        $wallet->save();

        return response()->json($wallet);
    }

    public function withdraw(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:0']);

        $wallet = auth()->user()->wallet;

        if ($wallet->balance < $request->amount) {
            return response()->json(['error' => 'Saldo insuficiente'], 400);
        }

        $wallet->balance -= $request->amount;
        $wallet->save();

        return response()->json($wallet);
    }

    public function transactions(Request $request)
    {
        // Validar la entrada
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id']
        ]);

        try {
            // Obtener los pagos y checkouts
            $payments = Payment::where('user_id', $data['user_id'])
                ->select('amount', 'description', 'created_at')
                ->get();

            $checkouts = Checkout::where('user_id', $data['user_id'])
                ->select('amount', 'product_type', 'product_details', 'created_at')
                ->get();

            // Inicializar una colección vacía para combinar resultados
            $combined = collect();

            // Combinar pagos
            if (count($payments)>0) {
                $combined = $combined->merge($payments->map(function ($payment) {
                    return [
                        'amount' => $payment->amount,
                        'description' => $payment->description,
                        'type' => 'payment',
                        'created_at' => \Carbon\Carbon::parse($payment->created_at)->format('d-m-Y'), // Formato d-m-Y
                    ];
                }));
            }

            // Combinar checkouts
            if (count($checkouts) > 0) {
                $combined = $combined->merge($checkouts->map(function ($checkout) {
                    return [
                        'amount' => $checkout->amount,
                        'description' => $this->getCheckoutDescription($checkout),
                        'type' => 'checkout',
                        'created_at' => \Carbon\Carbon::parse($checkout->created_at)->format('d-m-Y'), // Formato d-m-Y
                    ];
                }));
            }

            // Ordenar por fecha de creación (más reciente a más antiguo)
            $sorted = $combined->sortByDesc('created_at')->values()->all();

            return response()->json(['success' => true, 'data' => $sorted]);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json(['success' => false, 'message' => 'Error al obtener las transacciones: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener la descripción del checkout basado en el tipo de producto.
     */
    private function getCheckoutDescription(Checkout $checkout)
    {
        $productDetails = json_decode($checkout->product_details, true);

        switch ($checkout->product_type) {
            case 'course':
                return __('course_platform.buy') . ' ' . CursoPlataforma::findOrFail($productDetails['course_platform_id'])->name;

            case 'booking':
                $booking = Booking::findOrFail($productDetails['booking_id']);
                return optional($booking->event)->name ?? '';

            case 'e_book':
                return __('EBooks.buy') . ' ' . EBook::findOrFail($productDetails['booking_id'])->title;

            default:
                return null;
        }
    }
}
