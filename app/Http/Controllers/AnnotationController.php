<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Anotacion;
use App\Models\Letra;

class AnnotationController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'letra_id' => 'required|exists:letra,letra_id',
            'texto_seleccionado' => 'required|string',
            'explicacion' => 'required|string',
            'start_offset' => 'nullable|integer',
            'end_offset' => 'nullable|integer'
        ]);

        $anotacion = new Anotacion();
        $anotacion->letra_id = $request->letra_id;
        $anotacion->usuario_id = Auth::id();
        $anotacion->texto_seleccionado = $request->texto_seleccionado;
        $anotacion->explicacion = $request->explicacion;
        $anotacion->start_offset = $request->start_offset;
        $anotacion->end_offset = $request->end_offset;
        $anotacion->estado = 'aprobada'; // Auto-approve for now or 'pendiente' if you want moderation
        $anotacion->save();

        // return valid JSON that can be used to update DOM
        return response()->json([
            'success' => true,
            'anotacion' => $anotacion,
            'user' => Auth::user()->nombre_usuario
        ]);
    }

    public function show($id)
    {
        $anotacion = Anotacion::with('usuario')->findOrFail($id);
        
        return response()->json([
            'texto_seleccionado' => $anotacion->texto_seleccionado,
            'explicacion' => $anotacion->explicacion,
            'autor' => $anotacion->usuario->nombre_usuario,
            'fecha' => $anotacion->fecha_creacion
        ]);
    }
}
