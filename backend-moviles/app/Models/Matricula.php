<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'grado_id', 
        'anio'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }
}