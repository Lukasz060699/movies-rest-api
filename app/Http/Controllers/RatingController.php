<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Models\Movies;
use App\Models\Ratings;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        try { 

            if(!Auth::check()){
                throw ValidationException::withMessages(['message' => 'Non-authenticated user.']);
            }

            $movie = Movies::find($id);
            if(!$movie){
                throw ValidationException::withMessages(['message' => 'The film does not exist.']);
            }

            $existingRating = Ratings::where('user_id', Auth::id())->where('movie_id', $id)->first();
            if($existingRating){
                throw ValidationException::withMessages(['message' => 'A user has already rated this movie.']);
            }

            $data = $request->validate([
                'rating_value' => 'required|integer|between:1,5',
            ]);
            
            $rating = Ratings::create([
                'movie_id' => $id,
                'user_id' => Auth::id(),
                'rating_value' => $data['rating_value'],
            ]);

            $averageRating = Ratings::where('movie_id', $id)->avg('rating_value');
            $movie->average_rating = $averageRating;
            $movie->save();

        return response()->json($rating, 201);
    } catch (ValidationException $e) {
        return response()->json(['message' => $e->validator->errors()->first()], 400);
    } catch (QueryException $e) {
        return response()->json(['message' => 'An error occurred while processing your request.'], 500);
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if (!Auth::check()) {
                throw ValidationException::withMessages(['message' => 'Non-authenticated user.']);
            }

            $movie = Movies::find($id);
            if(!$movie){
                throw ValidationException::withMessages(['message' => 'The film does not exist.']);
            }
    
            $rating = Ratings::where('user_id', Auth::id())->where('movie_id', $id)->first();
            if (!$rating) {
                throw ValidationException::withMessages(['message' => 'User has not rated this movie.']);
            }
    
            $rating->delete();
            $averageRating = Ratings::where('movie_id', $id)->avg('rating_value');
            $movie->average_rating = $averageRating;
            $movie->save();

            return response()->json(['message' => 'Rating deleted successfully.'], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->validator->errors()->first()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while processing your request.'], 500);
        }
    }
}
