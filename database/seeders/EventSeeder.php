<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\EventController;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Http\Requests\Api\Event\StoreRequest;
use Illuminate\Http\Request;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener la fecha actual
        $now = Carbon::now();

        // Generar datos de prueba
        $events = [
            [
                'name' => 'Consulta Médica',
                'date' => $now->toDateString(), // Fecha actual
                'end_date' => $now->addDays(5)->toDateString(), // 5 días después
                'slug' => Str::slug('Consulta Médica'),
                'user_id' => 3, // Asegúrate de que este ID exista en la tabla users
                'description' => 'Consulta médica para revisión general.',
                'location' => 'Clínica Central',
                'tipo' => 'medico',
                'status' => true,
                'pet_id' => 3, // Asegúrate de que este ID exista en la tabla pets
                'owner_id' => [13], // Asegúrate de que estos IDs existan en la tabla users
                'image' => null, // O puedes poner una ruta válida a una imagen
                'service_id' => 1, // Asegúrate de que este ID exista en la tabla services
                'category_id' => 1, // Asegúrate de que este ID exista en la tabla categories
                'duration_id' => null, // Asegúrate de que este ID exista en la tabla service_duration
                'training_id' => null,
            ],
            [
                'name' => 'Entrenamiento Canino',
                'date' => $now->toDateString(),
                'end_date' => $now->addDays(5)->toDateString(),
                'slug' => Str::slug('Entrenamiento Canino'),
                'user_id' => 4,
                'description' => 'Entrenamiento básico para perros.',
                'location' => 'Parque Municipal',
                'tipo' => 'entrenamiento',
                'status' => true,
                'pet_id' => 4,
                'owner_id' => [24],
                'image' => null,
                'service_id' => null,
                'category_id' => null,
                'duration_id' => 1,
                'training_id' => 1, // Asegúrate de que este ID exista en la tabla service_training
            ],
            [
                'name' => 'Evento Benéfico',
                'date' => $now->toDateString(),
                'end_date' => $now->addDays(5)->toDateString(),
                'slug' => Str::slug('Evento Benéfico'),
                'user_id' => 5,
                'description' => 'Evento para recaudar fondos para animales necesitados.',
                'location' => 'Centro Comunitario',
                'tipo' => 'evento',
                'status' => true,
                'pet_id' => null,
                'owner_id' => [3],
                'image' => null,
                'service_id' => null,
                'category_id' => null,
                'duration_id' => null,
                'training_id' => null,
            ],
        ];
        $eventController = new EventController();

        // Insertar datos en la tabla events y llamar al método store
        foreach ($events as $eventData) {
            // Crear una instancia de Request con los datos del evento
            $request = Request::create('/events', 'POST', $eventData);

            // Crear una instancia de StoreRequest usando los datos del request
            $storeRequest = new StoreRequest();
            $storeRequest->setLaravelSession(app('session')->getId()); // Configurar la sesión si es necesario
            $storeRequest->replace($request->all()); // Reemplaza los datos del request

            // Llamar al método store del EventController
            try {
                // Validar manualmente antes de llamar al store
                if ($storeRequest->validate()) {
                    $eventController->store($storeRequest);
                    \Log::info('Evento creado: '.$eventData['name']);
                }
            } catch (\Exception $e) {
                \Log::error('Error al crear evento: '.$e->getMessage());
            }
        }
    }
}
