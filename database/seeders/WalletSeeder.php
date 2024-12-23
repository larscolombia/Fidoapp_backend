<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener todos los usuarios
        $users = User::all();

        foreach ($users as $user) {
            // Crear una wallet para cada usuario si no existe
            Wallet::firstOrCreate([
                'user_id' => $user->id,
            ], [
                'balance' => 0, // Puedes establecer un saldo inicial si lo deseas
            ]);
        }
    }
}
