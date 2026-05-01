<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // 👈 Agregar esta línea
use App\Http\Controllers\InicioController;
use App\Http\Controllers\NosotrosController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SostenibilidadController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\LibroReclamacionController;

// Rutas públicas del sitio web
Route::get('/', [InicioController::class, 'index'])->name('home');
Route::get('/nosotros', [NosotrosController::class, 'index'])->name('nosotros');
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/{slug}', [ProductoController::class, 'show'])->name('productos.show');
Route::get('/sostenibilidad', [SostenibilidadController::class, 'index'])->name('sostenibilidad');
Route::get('/experiencia', [SostenibilidadController::class, 'experiencia'])->name('experiencia');
Route::get('/concretips', [SostenibilidadController::class, 'concretips'])->name('concretips');
Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto.index');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');
Route::get('/libro-reclamaciones', [LibroReclamacionController::class, 'index'])->name('reclamaciones.index');
Route::post('/libro-reclamaciones', [LibroReclamacionController::class, 'store'])->name('reclamaciones.store');

// Ruta de logout usando Auth facade
Route::post('/logout', function () {
    Auth::logout(); // 👈 Usar Auth::logout() en lugar de auth()->logout()
    return redirect('/');
})->name('logout');