<?php

namespace App\Http\Controllers;

use App\Models\Genres;
use Illuminate\Http\Request;

class GenreController extends Controller
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
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate user input
        $data = $request->validate([
            'name' => 'required|string|unique:genres,name',
        ]);

        // Create a new genre
        $genre = Genres::create([
            'name' => $data['name'],
        ]);

        // Return a response indicating successful creation
        return response()->json($genre, 201);
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
        // Find the genre by ID
        $genre = Genres::findOrFail($id);

        // Check if the genre has associated movies
        if ($genre->movies->count() > 0) {
            return response()->json(['message' => 'Cannot delete category with associated movies.'], 403);
        }

        // Delete the genre
        $genre->delete();

        // Return a response indicating successful deletion
        return response()->json(null, 204);
    }
}
