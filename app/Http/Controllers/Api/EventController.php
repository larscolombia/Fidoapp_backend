<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Event\UpdateRequest;
use App\Http\Requests\Api\Event\StoreRequest;
use Modules\Event\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
     /**
     * Obtener todos los eventos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $events = Event::all();

        return response()->json([
            'success' => true,
            'message' => 'Eventos recuperados exitosamente',
            'data' => $events,
        ]);
    }

    public function getEventsByUser($user_id)
    {
        $events = Event::where('user_id', $user_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Eventos recuperados exitosamente',
            'data' => $events
        ]);
    }

    public function store (StoreRequest $request) {
        $validatedData = $request->validated();

        $event = Event::create([
            'name' => $request->input('name'),
            'date' => $request->input('date'),
            'slug' => $request->input('slug'),
            'user_id' => $request->input('user_id'),
            'description' => $request->input('description'),
            'location' => $request->input('location'),
            'tipo' => $request->input('tipo'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Evento creado exitosamente',
            'data' => $event,
        ], 201);
    }

    public function update (UpdateRequest $request, $id) {
        $event = Event::findOrFail($id);

        $event->update([
            'name' => $request->input('name', $event->name),
            'date' => $request->input('date', $event->date),
            'slug' => $request->input('slug', $event->slug),
            'user_id' => $request->input('user_id', $event->user_id),
            'description' => $request->input('description', $event->description),
            'location' => $request->input('location', $event->location),
            'tipo' => $request->input('tipo', $event->tipo),
            'status' => $request->input('status', $event->status),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Evento actualizado exitosamente',
            'data' => $event,
        ]);
    }

    /**
     * Mostrar un evento específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Evento recuperado exitosamente',
            'data' => $event,
        ]);
    }

    /**
     * Eliminar un evento específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evento eliminado exitosamente',
        ]);
    }
}
