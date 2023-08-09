<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genres extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'];


    public function movies(){
        return $this->belongsToMany(Movies::class, 'movie_genre', 'genre_id', 'movie_id');
    }
}
