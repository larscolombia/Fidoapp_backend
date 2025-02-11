<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->after('last_name')->nullable();
        });

        $this->insertSlug();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }

    private function insertSlug()
    {
        // Obtener todos los usuarios
        $users = User::all();

        foreach ($users as $user) {
            // Generar un slug basado en first_name y last_name
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
    }
};
