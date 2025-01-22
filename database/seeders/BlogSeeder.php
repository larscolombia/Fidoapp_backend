<?php

namespace Database\Seeders;

use Modules\Blog\Models\Blog;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $blogs = [
            [
                'name' => 'Guía Completa para el Cuidado de Mascotas',
                'description' => 'En este blog, exploraremos los aspectos esenciales del cuidado de mascotas, desde la alimentación adecuada hasta la importancia del ejercicio y la atención veterinaria regular.',
                'tags' => 'cuidado, mascotas, salud',
                'status' => true,
                'created_by' => 1, // Asegúrate de que este ID exista en tu tabla de usuarios
                'updated_by' => null,
                'deleted_by' => null,
            ],
            [
                'name' => 'Los Mejores Consejos para Entrenar a tu Perro',
                'description' => 'Descubre los mejores consejos y técnicas para entrenar a tu perro de manera efectiva y positiva. Aprende a usar comandos básicos y refuerzos positivos.',
                'tags' => 'entrenamiento, perros, consejos',
                'status' => true,
                'created_by' => 2, // Asegúrate de que este ID exista en tu tabla de usuarios
                'updated_by' => null,
                'deleted_by' => null,
            ],
        ];

        foreach ($blogs as $blog) {
            Blog::create($blog);
        }
    }
}
