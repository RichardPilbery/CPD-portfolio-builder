<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airway extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function audit() {

        return $this->belongsTo(Audit::class);

    }

    public function airwayactivitytypes() {

        return $this->hasOne(AirwayActivityType::class);

    }

    public function capnographies() {

        return $this->hasOne(Capnography::class);

    }
}
