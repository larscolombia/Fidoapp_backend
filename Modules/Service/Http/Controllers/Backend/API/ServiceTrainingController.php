<?php

namespace Modules\Service\Http\Controllers\Backend\API;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Service\Models\ServiceTraining;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Requests\Api\ServiceTrainingStoreRequest;
use App\Http\Requests\Api\ServiceTrainingUpdateRequest;
use Modules\Service\Transformers\ServiceTrainingResource;

class ServiceTrainingController extends Controller
{
    public function trainingList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $servicetraining =  ServiceTraining::where('status', 1);

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

        if ($request->hasFile('image')) {
            // Obtener el archivo de imagen
            $image = $request->file('image');

            // Crear un nombre único para la imagen
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Guardar la imagen en public/images/service_training
            $image->move(public_path('images/service_training'), $imageName);

            // Agregar el nombre de la imagen a los datos validados
            $validatedData['image'] = 'images/service_training/' . $imageName;
        }
        // Crear el registro en la base de datos
        $data = ServiceTraining::create($validatedData);

        // Mensaje de éxito
        $message = __('messages.created_service_training');

        // Devolver la respuesta JSON
        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function show($id)
    {
        // Buscar el registro por ID
        $serviceTraining = ServiceTraining::findOrFail($id);
        if ($serviceTraining->image) {
            $serviceTraining->image = asset($serviceTraining->image);
        }
        // Devolver la respuesta JSON con el registro encontrado
        return response()->json([
            'data' => $serviceTraining,
            'status' => true,
        ], 200);
    }

    public function update(ServiceTrainingUpdateRequest $request, $id)
    {
        try {
            // Obtener los datos validados
            $validatedData = $request->validated();

            // Buscar el registro existente
            $serviceTraining = ServiceTraining::findOrFail($id);

            // Generar el nuevo slug a partir del nombre
            $slug = Str::slug($validatedData['name'], '-');
            $originalSlug = $slug;

            // Verificar la unicidad del slug y modificarlo si es necesario
            $counter = 1;
            while (ServiceTraining::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            // Asignar el nuevo slug a los datos validados
            $validatedData['slug'] = $slug;

            if ($request->hasFile('image')) {
                // Obtener el archivo de imagen
                $image = $request->file('image');

                // Crear un nombre único para la imagen
                $imageName = time() . '.' . $image->getClientOriginalExtension();

                // Guardar la imagen en public/images/service_training
                $image->move(public_path('images/service_training'), $imageName);

                // Agregar el nombre de la imagen a los datos validados
                $validatedData['image'] = 'images/service_training/' . $imageName;
            }
            // Actualizar el registro con los datos validados
            $serviceTraining->update($validatedData);

            // Mensaje de éxito
            $message = __('messages.updated_service_training');

            // Devolver la respuesta JSON
            return response()->json(['message' => $message, 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    'success' => false
                ]
            );
        }
    }

    public function destroy($id)
    {
        try {
            // Buscar el registro por ID
            $serviceTraining = ServiceTraining::findOrFail($id);

            // Eliminar el registro
            $serviceTraining->delete();

            // Mensaje de éxito
            $message = __('messages.deleted_service_training');

            // Devolver la respuesta JSON
            return response()->json(['success' => true, 'message' => $message, 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    'success' => false
                ]
            );
        }
    }

    public function trainingListAll(Request $request)
    {

        $servicetraining =  ServiceTraining::where('status', 1);

        $servicetraining = $servicetraining->get();
        $items = ServiceTrainingResource::collection($servicetraining);

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('service.training_list'),
        ], 200);
    }

}
