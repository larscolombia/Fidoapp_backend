<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Checkout;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function store(Request $request, $amount)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'course_platform_id' => ['sometimes', 'exists:courses_platform,id'],
            'booking_id' => ['sometimes', 'exists:bookings,id'],
            'e_book_id' => ['sometimes', 'exists:e_book', 'id']
        ]);

        $wallet = Wallet::where('user_id', $data['user_id'])->first();
        if (!$wallet) {
            return ['success' => false, 'error' => 'Wallet not found'];
        }
        $errorBalance = $this->checkBalance($wallet,$amount);
        if (!$errorBalance['success']) {
            return $errorBalance; // Retorna el array de error
        }
        // Determinar el tipo de producto y los detalles
        $productType = null;
        $productDetails = [];

        if (isset($data['course_platform_id'])) {
            $productType = 'course';
            $productDetails['course_platform_id'] = $data['course_platform_id'];
        } elseif (isset($data['booking_id'])) {
            $productType = 'booking';
            $productDetails['booking_id'] = $data['booking_id'];
        } elseif (isset($data['e_book_id'])) {
            $productType = 'e_book';
            $productDetails['e_book_id'] = $data['e_book_id'];
        }

        // Registrar la compra
        $checkout = Checkout::create([
            'user_id' => $data['user_id'],
            'amount' => $amount,
            'product_type' => $productType,
            'product_details' => json_encode($productDetails),
        ]);

        // Actualizar el saldo de la billetera
        $wallet->balance -= $amount;
        $wallet->save();

        return ['success' => true, 'message' => 'Purchase successful', 'checkout' => $checkout];
    }

    public function checkBalance($wallet,$amount)
    {
        if ($amount > $wallet->balance) {
            return ['success' => false, 'error' => 'Insufficient balance'];
        }
        return ['success' => true];
    }
}
