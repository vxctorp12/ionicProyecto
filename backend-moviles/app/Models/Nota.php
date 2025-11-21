<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model {
    protected $fillable = ['matricula_id', 'actividad_id', 'valor'];

    public function actividad() {
        return $this->belongsTo(Actividad::class);
    }
}