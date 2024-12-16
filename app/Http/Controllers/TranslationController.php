<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class TranslationController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el idioma actual
        $locale = isset($request->language) ? $request->language : App::getLocale();
        // Cargar las traducciones del archivo de validación
        $translations = trans('validation', [], $locale);
        // Devolver las traducciones como JSON
        return response()->json($translations);
    }
}
