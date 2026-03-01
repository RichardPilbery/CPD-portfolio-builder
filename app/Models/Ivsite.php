<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ivsite extends Model
{
    use HasFactory;

    public function vascular() {
        return $this->belongsToMany(Vascular::class);
    }
}
