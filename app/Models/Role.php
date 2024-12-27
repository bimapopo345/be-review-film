<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public $incrementing = false; // Karena menggunakan UUID
    protected $keyType = 'string'; // Karena menggunakan UUID

    protected $fillable = [
        'id',
        'name',
    ];

    /**
     * Relasi dengan User
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
