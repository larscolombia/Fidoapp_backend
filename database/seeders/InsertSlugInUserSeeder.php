<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class InsertSlugInUserSeeder extends Seeder
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
}
