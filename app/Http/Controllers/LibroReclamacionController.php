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
            'claimant_name' => 'required|string',
            'document_type' => 'required|string',
            'document_number' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'nullable|string',
            'order_number' => 'nullable|string',
            'claim_description' => 'required|string',
            'petition' => 'nullable|string',
        ]);
        
        LibroReclamacion::create($validated);
        
        return redirect()->back()->with('success', 'Reclamación registrada correctamente');
    }
}