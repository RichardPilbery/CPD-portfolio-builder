<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'pin', 'service_id', 'role_id'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id');
    }

    public function summary()
    {
        return $this->hasOne(Summary::class);
    }

    public function activity()
    {
        return $this->hasOne(Service::class, 'service_id');
    }

    public function roles()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function audits()
    {
        return $this->hasMany(Audit::class);
    }

    public function ksfs()
    {
        return $this->belongsToMany(Ksf::class);
    }

    public function clfs()
    {
        return $this->belongsToMany(Clf::class);
    }

    public function pdps()
    {
        return $this->hasMany(Pdp::class);
    }
}
