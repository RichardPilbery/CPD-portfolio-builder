<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ivtype extends Model
{
    use HasFactory;

    public function vascular() {
        return $this->belongsToMany(Vascular::class);
    }
}
