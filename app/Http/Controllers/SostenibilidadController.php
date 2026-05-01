<?php

namespace App\Http\Controllers;

use App\Models\Sostenibilidad;

class SostenibilidadController extends Controller
{
    public function index()
    {
        $sostenibilidad = Sostenibilidad::getActiveContent();
        return view('sostenibilidad.index', compact('sostenibilidad'));
    }
    
    /**
     * Página de Experiencia / Proyectos Realizados
     */
    public function experiencia()
    {
        $sostenibilidad = Sostenibilidad::getActiveContent();
        $proyectos = [];
        
        if ($sostenibilidad && $sostenibilidad->proyectos_realizados) {
            $proyectos = is_array($sostenibilidad->proyectos_realizados) 
                ? $sostenibilidad->proyectos_realizados 
                : json_decode($sostenibilidad->proyectos_realizados, true);
        }
        
        return view('sostenibilidad.experiencia', compact('sostenibilidad', 'proyectos'));
    }
    
    /**
     * Página de Tips / Recomendaciones Técnicas
     */
    public function concretips()
    {
        $sostenibilidad = Sostenibilidad::getActiveContent();
        $recomendaciones = [];
        
        if ($sostenibilidad && $sostenibilidad->recomendaciones_tecnicas) {
            $recomendaciones = is_array($sostenibilidad->recomendaciones_tecnicas) 
                ? $sostenibilidad->recomendaciones_tecnicas 
                : json_decode($sostenibilidad->recomendaciones_tecnicas, true);
        }
        
        return view('sostenibilidad.concretips', compact('sostenibilidad', 'recomendaciones'));
    }
}