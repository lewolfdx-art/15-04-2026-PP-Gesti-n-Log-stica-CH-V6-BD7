<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibroReclamacion extends Model
{
    protected $table = 'libro_reclamacions';
    
    protected $fillable = [
        'claimant_name', 'document_type', 'document_number', 'email',
        'phone', 'address', 'order_number', 'claim_description',
        'petition', 'status', 'response'
    ];
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function resolve($response)
    {
        $this->update([
            'status' => 'resolved',
            'response' => $response,
            'resolved_at' => now()
        ]);
    }
}