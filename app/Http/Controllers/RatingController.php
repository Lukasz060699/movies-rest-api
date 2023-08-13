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
     * Store a newly created movie rating in storage.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param int $id The ID of the movie.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $id)
    {
        try { 

            // Check if the user is authenticated
            if(!Auth::check()){
                throw ValidationException::withMessages(['message' => 'Non-authenticated user.']);
            }

            // Find the movie by ID
            $movie = Movies::find($id);
            if(!$movie){
                throw ValidationException::withMessages(['message' => 'The film does not exist.']);
            }

            // Check if the user has already rated this movie
            $existingRating = Ratings::where('user_id', Auth::id())->where('movie_id', $id)->first();
            if($existingRating){
                throw ValidationException::withMessages(['message' => 'A user has already rated this movie.']);
            }

            // Validate the request data
            $data = $request->validate([
                'rating_value' => 'required|integer|between:1,5',
            ]);
            
            // Create a new rating
            $rating = Ratings::create([
                'movie_id' => $id,
                'user_id' => Auth::id(),
                'rating_value' => $data['rating_value'],
            ]);

            // Calculate and update the average rating for the movie
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
     * Remove the specified movie rating from storage.
     *
     * @param string $id The ID of the movie.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            // Check if the user is authenticated
            if (!Auth::check()) {
                throw ValidationException::withMessages(['message' => 'Non-authenticated user.']);
            }

             // Find the movie by ID
            $movie = Movies::find($id);
            if(!$movie){
                throw ValidationException::withMessages(['message' => 'The film does not exist.']);
            }
    
            // Check if the user has rated this movie
            $rating = Ratings::where('user_id', Auth::id())->where('movie_id', $id)->first();
            if (!$rating) {
                throw ValidationException::withMessages(['message' => 'User has not rated this movie.']);
            }
    
            // Delete the rating and update the average rating
            $rating->delete();
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
