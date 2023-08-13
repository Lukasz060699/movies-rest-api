<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movies extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'cover_image', 
        'description', 
        'producion_country'];

    public function genres(){
        return $this->belongsToMany(Genres::class, 'movie_genre', 'movie_id', 'genre_id');
    }

    public function ratings(){
        return $this->hasMany(Ratings::class, 'movie_id');
    }
    
}
