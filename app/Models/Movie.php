<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $table = 'movie';
    public $incrementing = false; 
    protected $keyType = 'string'; 

    protected $fillable = [
        'id',
        'title',
        'summary',
        'poster',
        'year',
        'genre_id',
    ];

    // Relasi dengan Genre
    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }
}
