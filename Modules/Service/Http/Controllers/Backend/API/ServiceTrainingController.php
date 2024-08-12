<?php

namespace Modules\Service\Http\Controllers\Backend\API;

use App\Http\Requests\Api\ServiceTrainingStoreRequest;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Service\Models\ServiceTraining;
use Modules\Service\Transformers\ServiceTrainingResource;

class ServiceTrainingController extends Controller
{
    public function trainingList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $servicetraining =  ServiceTraining::where('status',1);
      
        if ($request->has('search')) {
            $servicetraining->where('name', 'like', "%{$request->search}%")
                            ->orWhere('description', 'like', "%{$request->search}%");
        }

        $servicetraining = $servicetraining->paginate($perPage);
        $items = ServiceTrainingResource::collection($servicetraining);
      

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('service.training_list'),
        ], 200);
    }


     /**
     * Crea.
     *
     * @param \App\Http\Requests\StoreServiceTrainingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ServiceTrainingStoreRequest $request)
    {
       // Obtener los datos validados
       $validatedData = $request->validated();

       // Generar el slug a partir del name
       $slug = Str::slug($validatedData['name'], '-');
       $originalSlug = $slug;

       // Verificar la unicidad del slug y modificarlo si es necesario
       $counter = 1;
       while (ServiceTraining::where('slug', $slug)->exists()) {
           $slug = $originalSlug . '-' . $counter++;
       }

       $validatedData['slug'] = $slug;

       // Añadir el usuario que crea el registro
       $validatedData['created_by'] = auth()->id();

       // Crear el registro en la base de datos
       $data = ServiceTraining::create($validatedData);

       // Mensaje de éxito
       $message = __('messages.created_service_training');

       // Devolver la respuesta JSON
       return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function update () {
        
    }
}
