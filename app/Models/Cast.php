<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cast extends Model
{
    use HasFactory;

    // Tentukan nama tabel secara eksplisit
    protected $table = 'casts'; // pastikan sesuai dengan nama tabel di database

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'bio',
        'age'
    ];
}

