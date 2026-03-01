<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirwayActivityType extends Model
{
    use HasFactory;

    public function airways() {
        return $this->belongsToMany(Airway::class);
    }
}
