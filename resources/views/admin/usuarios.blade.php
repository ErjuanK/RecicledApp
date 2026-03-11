@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Success Message --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center" x-data="{ show: true }" x-show="show">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        {{ session('success') }}
        <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">&times;</button>
    </div>
    @endif

    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800 mb-4 sm:mb-0">Gestión de Usuarios</h3>
            <span class="text-sm text-gray-500">{{ $usuarios->total() }} usuarios en total</span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4 font-semibold">Usuario</th>
                        <th class="px-6 py-4 font-semibold">Email</th>
                        <th class="px-6 py-4 font-semibold text-center">Rol</th>
                        <th class="px-6 py-4 font-semibold">Fecha Registro</th>
                        <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($usuarios as $usuario)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm"
                                         src="{{ $usuario->avatar ? asset($usuario->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($usuario->nombre_usuario).'&background=6F00D0&color=fff' }}"
                                         alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $usuario->nombre_usuario }}</div>
                                    @if($usuario->nombre_real || $usuario->apellidos)
                                    <div class="text-xs text-gray-500">{{ $usuario->nombre_real }} {{ $usuario->apellidos }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-700">{{ $usuario->email }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($usuario->rol === 'admin')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-50 text-red-700">Admin</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700">{{ ucfirst($usuario->rol ?? 'usuario') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-500">{{ $usuario->fecha_registro ? $usuario->fecha_registro->format('d/m/Y') : '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            @if($usuario->rol !== 'admin')
                            <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario->usuario_id) }}"
                                  onsubmit="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400 italic">Protegido</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            No hay usuarios registrados aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($usuarios->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $usuarios->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
