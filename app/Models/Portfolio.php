<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = ['actdate'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function path()
    {
        return "/portfolio/{$this->id}";
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function activity()
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'subject');
    }

    public function clfs()
    {
        return $this->belongsToMany(Clf::class);
    }

    public function ksfs()
    {
        return $this->belongsToMany(Ksf::class);
    }

    public function swot()
    {
        return $this->hasOne(Swot::class);
    }

}
