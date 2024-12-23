<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
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
            $wallet = Wallet::where('user_id',$data['user_id'])->first();

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
}
