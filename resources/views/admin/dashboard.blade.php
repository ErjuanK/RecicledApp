@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Artistas -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Artistas Totales</h3>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-purple-900">{{ number_format($stats['artistas']) }}</span>
                <span class="text-green-500 text-sm font-medium flex items-center bg-green-50 px-2 py-1 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    12%
                </span>
            </div>
        </div>

        <!-- Card 2: Álbumes -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Álbumes Totales</h3>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-purple-900">{{ number_format($stats['albumes']) }}</span>
                <span class="text-green-500 text-sm font-medium flex items-center bg-green-50 px-2 py-1 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    5.4%
                </span>
            </div>
        </div>

        <!-- Card 3: Canciones -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Canciones Totales</h3>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-purple-900">{{ number_format($stats['canciones']) }}</span>
                <span class="text-green-500 text-sm font-medium flex items-center bg-green-50 px-2 py-1 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    8%
                </span>
            </div>
        </div>

        <!-- Card 4: Usuarios -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Usuarios Registrados</h3>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-purple-900">{{ number_format($stats['usuarios']) }}</span>
                <span class="text-green-500 text-sm font-medium flex items-center bg-green-50 px-2 py-1 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    24%
                </span>
            </div>
        </div>
    </div>

    <!-- Artist Management Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center bg-white">
            <h3 class="text-lg font-bold text-gray-800 mb-4 sm:mb-0">Gestión de Artistas</h3>
            <button class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-lg flex items-center transition-colors text-sm font-medium shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Añadir Artista
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4 font-semibold">Nombre</th>
                        <th class="px-6 py-4 font-semibold text-center">Álbumes</th>
                        <th class="px-6 py-4 font-semibold text-center">Canciones</th>

                        <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($artistas as $artista)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm" 
                                         src="{{ $artista->foto_url ? asset($artista->foto_url) : 'https://ui-avatars.com/api/?name='.urlencode($artista->nombre_artistico).'&background=6F00D0&color=fff' }}" 
                                         alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $artista->nombre_artistico }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-50 text-purple-700">
                                {{ $artista->albums_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-sm text-gray-600">{{ $artista->canciones_count ?? 0 }}</span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('artista.show', $artista->artista_id) }}" target="_blank"
                                   class="text-gray-400 hover:text-primary transition-colors p-1" title="Ver perfil">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                
                                <form method="POST" action="{{ route('admin.artistas.destroy', $artista->artista_id) }}"
                                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar este artista?')"
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                            No hay artistas registrados aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($artistas->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $artistas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
