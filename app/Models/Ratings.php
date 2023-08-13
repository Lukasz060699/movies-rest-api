<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratings extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'user_id',
        'rating_value'
    ];


    public function movie(){
        return $this->belongsTo(Movies::class, 'movie_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
