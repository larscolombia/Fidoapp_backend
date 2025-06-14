<?php

namespace App\Http\Controllers\Api;

use App\Models\Comando;
use Modules\Pet\Models\Pet;
use Illuminate\Http\Request;
use App\Models\CategoryComando;
use App\Http\Controllers\Controller;

class ComandoController extends Controller
{
    public function index()
    {
        $comandos = Comando::whereNull('pet_id')->get();
        return response()->json([
            'success' => true,
            'data' => $comandos,
            'message' => __('comando_entrenamiento.commands_list')
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:especializado,basico',
            'is_favorite' => 'required|boolean',
            'category_id' => 'required|exists:category_comandos,id',
            'voz_comando' => 'required|string|max:255',
            'instructions' => 'required|string',
            'pet_id' => 'required|exists:pets,id'
        ]);

        $comando = Comando::create($request->all());

        return response()->json(['success' => true, 'data' => $comando], 201);
    }

    public function show($id)
    {
        $comando = Comando::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $comando,
            'message' => __('comando_entrenamiento.get_command')
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $comando = Comando::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type' => 'sometimes|in:especializado,basico',
            'is_favorite' => 'sometimes|boolean',
            'category_id' => 'sometimes|exists:category_comandos,id',
            'voz_comando' => 'sometimes|string|max:255',
            'instructions' => 'sometimes|string',
            'pet_id' => 'required|exists:pets,id'
        ]);

        $comando->update($request->only('name', 'description', 'type', 'is_favorite', 'category_id', 'voz_comando', 'instructions'));

        return response()->json(['success' => true, 'data' => $comando], 200);
    }

    public function destroy($id)
    {
        $comando = Comando::findOrFail($id);
        $comando->delete();

        return response()->json([
            'success' => true,
            'message' => __('comando_entrenamiento.Comando deleted successfully'),
            'data' => $comando
        ], 200);
    }

    public function storeCommandUser(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'type' => 'required|in:especializado,basico',
                'is_favorite' => 'required|boolean',
                'category_id' => 'required|exists:category_comandos,id',
                'voz_comando' => 'required|string|max:255',
                'instructions' => 'required|string',
                'pet_id' => 'required|exists:pets,id'
            ]);
            $pet = Pet::find($data['pet_id']);
            $data['user_id'] = $pet->owner->id;
            $comando = Comando::create($data);
            return response()->json(['success' => true, 'data' => $comando], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Captura de errores de validación
            return response()->json([
                'message' => __('validation.failed'),
                'errors' => $e->validator->errors(),
                'success' => false
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateCommandUser(Request $request, $id)
    {
        try {
           $comando = Comando::findOrFail($id);
           $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'type' => 'required|in:especializado,basico',
                'is_favorite' => 'required|boolean',
                'category_id' => 'required|exists:category_comandos,id',
                'voz_comando' => 'required|string|max:255',
                'instructions' => 'required|string',
                'pet_id' => 'required|exists:pets,id'
            ]);
            $pet = Pet::find($data['pet_id']);
            $data['user_id'] = $pet->owner->id;
            if (is_null($comando->pet_id)) {
                $comando = Comando::create($data);
            } else {
                // Actualizar el comando existente si pet_id no es null
                $comando->update($request->only('name', 'description', 'type', 'is_favorite', 'category_id', 'voz_comando', 'instructions'));
            }

            return response()->json(['success' => true, 'data' => $comando], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Captura de errores de validación
            return response()->json([
                'message' => __('validation.failed'),
                'errors' => $e->validator->errors(),
                'success' => false
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCommandByUser(Request $request)
    {
        try {
            $data = $request->validate([
                'pet_id' => 'required|exists:pets,id'
            ]);

            // Obtener comandos donde pet_id sea igual al proporcionado o sea null
            $comandos = Comando::where('pet_id', $data['pet_id'])
                ->orWhereNull('pet_id')
                ->get();

            if ($comandos->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No se encontraron comandos para este pet_id.'], 404);
            }

            return response()->json(['success' => true, 'data' => $comandos], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Captura de errores de validación
            return response()->json([
                'message' => __('validation.failed'),
                'errors' => $e->validator->errors(),
                'success' => false
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCategoryCommand()
    {
        $categorieCommands = CategoryComando::all();
        return response()->json(['success' => true, 'data' => $categorieCommands], 200);
    }

    public function setLearned(Request $request)
    {
        try{
            $data = $request->validate([
                'comando_id' => ['required','exists:comandos,id'],
                'pet_id' => ['required','exists:pets,id'],
                'learned' => ['required','boolean']
            ]);
            $command = Comando::where('id',$data['comando_id'])
                ->where('pet_id',$data['pet_id'])
                ->first();
            if(!$command){
                return response()->json(['success' => false, 'message' => __('messages.no_record')], 404);
            }

            $command->learned = $data['learned'];
            $command->save();
            return response()->json(['success' => true, 'data' => $command], 200);
        }catch (\Illuminate\Validation\ValidationException $e) {
            // Captura de errores de validación
            return response()->json([
                'message' => __('validation.failed'),
                'errors' => $e->validator->errors(),
                'success' => false
            ], 422);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}
