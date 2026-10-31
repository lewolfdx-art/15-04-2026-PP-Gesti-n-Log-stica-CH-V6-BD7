<?php

namespace App\Http\Controllers;

use App\Models\LibroReclamacion;
use Illuminate\Http\Request;

class LibroReclamacionController extends Controller
{
    public function index()
    {
        return view('libro-reclamaciones.index');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'claimant_name' => 'required|string|max:255',
            'document_type' => 'required|string|max:30',
            'document_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'address' => 'nullable|string|max:500',
            'order_number' => 'nullable|string|max:100',
            'claim_description' => 'required|string|max:5000',
            'petition' => 'nullable|string|max:5000',
        ]);
        
        LibroReclamacion::create($validated);
        
        return redirect()->back()->with('success', 'Reclamación registrada correctamente');
    }
}