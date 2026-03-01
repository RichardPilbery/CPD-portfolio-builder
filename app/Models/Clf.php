<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clf extends Model
{
    use HasFactory;

    // prevent use of plural name in this case clves
    protected $table = 'clfs';

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }
}
