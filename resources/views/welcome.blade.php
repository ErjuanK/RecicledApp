@extends('layouts.app')

@section('content')
<div class="h-screen flex flex-col items-center justify-center text-center">
    <div class="bg-white p-10 rounded-lg shadow-xl border-t-4 border-purple-600 max-w-2xl w-full mx-4">
        <h1 class="text-4xl text-purple-700 font-bold mb-4">
            Bienvenido a R E C I C L E D ☆
        </h1>
        <p class="text-gray-600 text-lg mb-8">
            El proyecto ha sido migrado visualmente a Laravel.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-6 border rounded-lg hover:border-purple-300 transition cursor-pointer group">
                <i class="fa-solid fa-paintbrush text-3xl text-gray-400 group-hover:text-purple-600 mb-3"></i>
                <h3 class="font-bold text-gray-700">Diseño</h3>
                <p class="text-sm text-gray-500">Assets y Estilos cargados correctamente.</p>
            </div>
            
            <div class="p-6 border rounded-lg hover:border-purple-300 transition cursor-pointer group">
                <i class="fa-solid fa-database text-3xl text-gray-400 group-hover:text-purple-600 mb-3"></i>
                <h3 class="font-bold text-gray-700">Base de Datos</h3>
                <p class="text-sm text-gray-500">Conexión: {{ config('session.driver') == 'file' ? 'Modo Archivo (DB Pendiente)' : 'Activa' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
