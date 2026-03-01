<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ksf extends Model
{
    use HasFactory;

    // Stop use of plural name
    protected $table = 'ksfs';

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
