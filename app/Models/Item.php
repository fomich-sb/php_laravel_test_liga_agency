<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name', 'status', 'description'];
    
    public function scopeAllowed($query)
    {
        return $query->where('status', 'Allowed');
    }
}