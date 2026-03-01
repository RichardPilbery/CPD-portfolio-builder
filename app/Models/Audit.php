<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Audit extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function path()
    {
        return "/audit/{$this->id}";
    }


    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'subject');
    }

    public function audititems()
    {
        return $this->belongsToMany(Audititem::class);
    }

    public function airways()
    {
        return $this->hasMany(Airway::class);
    }

    public function vasculars()
    {
        return $this->hasMany(Vascular::class);
    }
}
