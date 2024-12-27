<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $table = 'reviews';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
      'id','critic','rating','user_id','movie_id'
    ];
}
