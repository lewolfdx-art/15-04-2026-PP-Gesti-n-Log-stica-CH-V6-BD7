<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $table = 'contactos';
    
    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message', 'is_read'
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
    ];
    
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}