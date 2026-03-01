<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vascular extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function audit() {

        return $this->belongsTo(Audit::class);

    }

    public function ivsite() {
        return $this->hasOne(Ivsite::class);
    }

    public function ivtype() {
        return $this->hasOne(Ivtype::class);
    }
}
