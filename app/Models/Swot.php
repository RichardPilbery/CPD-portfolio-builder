<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Swot extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function ksf() {
        return $this->belongsToMany(Ksf::class, 'id', 'user_id');
    }

}
