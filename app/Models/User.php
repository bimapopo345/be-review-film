<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str; // Tidak perlu jika tidak digunakan di model
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    public $incrementing = false; // Tambahkan ini
    protected $keyType = 'string'; // Tambahkan ini

    protected $fillable = [
        'id','name','email','password','role_id','email_verified_at'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Pastikan Laravel Anda mendukung 'hashed' jika menggunakan Laravel 10+
    ];

    /**
     * Mendapatkan identifier yang akan dimasukkan ke dalam JWT
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Mendapatkan custom claims untuk JWT
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Relasi dengan Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relasi dengan Profile
     */
    // Jika Anda tidak memiliki model Profile, sebaiknya hapus atau komentari
    // public function profile()
    // {
    //     return $this->hasOne(Profile::class);
    // }
}
