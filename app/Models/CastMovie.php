<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastMovie extends Model
{
    use HasFactory;
    protected $table = 'cast_movie';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id','name','cast_id','movie_id'];

    public function cast()
    {
        return $this->belongsTo(Cast::class,'cast_id');
    }
    public function movie()
    {
        return $this->belongsTo(Movie::class,'movie_id');
    }
}
