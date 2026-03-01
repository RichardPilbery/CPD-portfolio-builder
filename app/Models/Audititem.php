<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audititem extends Model
{
    use HasFactory;

    public function audits()
    {
        return $this->belongsToMany(Audit::class);
    }
}
