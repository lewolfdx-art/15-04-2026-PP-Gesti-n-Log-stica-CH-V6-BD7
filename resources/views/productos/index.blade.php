@extends('layouts.app')

@section('title', 'Productos - Concretera Huancayo')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Nuestros Productos</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($productos as $producto)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    @if($producto->image)
                        <img src="{{ Storage::url($producto->image) }}" 
                             alt="{{ $producto->name }}"
                             class="w-full h-48 object-cover">
                    @endif
                    <div class="p-4">
                        <h3 class="text-xl font-semibold mb-2">{{ $producto->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($producto->description, 100) }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-red-700">
                                S/ {{ number_format($producto->price, 2) }}
                            </span>
                            <a href="{{ route('productos.show', $producto->slug) }}" 
                               class="bg-blue-900 text-white px-4 py-2 rounded hover:bg-blue-800">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{ $productos->links() }}
    </div>
@endsection