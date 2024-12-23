<?php

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();
        });

        $this->create();
    }

    private function create()
    {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallets');
    }
};
