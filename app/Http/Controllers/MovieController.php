<?php

namespace App\Http\Controllers;

use App\Models\Movies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movies::with('genres')->get();
        return response()->json($movies);
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
    public function store(Request $request)
    {

        $data = $request->validate([
            'title' => 'required|string',
            'cover_image' => 'required|image',
            'description' => 'required|string',
            'producion_country' => 'required|string',
            'genres.*' => 'exists:genres,id',
        ]);

        $coverPath = $request->file('cover_image')->store('covers', 'public');
        $movie = Movies::create([
            'title' => $data['title'],
            'cover_image' => $coverPath,
            'description' => $data['description'],
            'producion_country' => $data['producion_country'],
        ]);

        $movie->genres()->sync($data['genres']);

        return response()->json($movie, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $movie = Movies::with('genres')->findOrFail($id);
        return response()->json($movie);
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
        $data = $request->validate([
            'title' => 'string',
            'cover_image' => 'image',
            'description' => 'string',
            'producion_country' => 'string',
            'genres' => 'array',
            'genres.*' => 'exists:genres,id',
        ]);

        $movie = Movies::findOrFail($id);

        if ($request->hasFile('cover_image')) {
            
            $coverPath = $request->file('cover_image')->store('covers', 'public');
            
            $scaledCoverPath = 'scaled_' . $coverPath;
            $this->resizeImage(storage_path('app/public/' . $coverPath), storage_path('app/public/' . $scaledCoverPath), 300, 450); 

            Storage::disk('public')->delete($movie->cover_image);
            $movie->cover_image = $scaledCoverPath;
        }

        $movie->update([
            'title' => $data['title'] ?? $movie->title,
            'description' => $data['description'] ?? $movie->description,
            'producion_country' => $data['producion_country'] ?? $movie->producion_country,
        ]);

        if (isset($data['genres'])) {
            $movie->genres()->sync($data['genres']);
        }

        return response()->json($movie);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $movie = Movies::findOrFail($id);
        $movie->ratings()->delete();
        $movie->genres()->detach();
        Storage::disk('public')->delete($movie->cover_image);
        $movie->delete();
        return response()->json(null, 204);
    }

    public function searchByTitle(Request $request)
    {
        $title = $request->input('title');

        $movies = Movies::with('genres')
            ->where('title', 'like', '%' . $title . '%')
            ->get();

        return response()->json($movies);
    }

    private function resizeImage($sourcePath, $destinationPath, $width, $height)
    {
        list($sourceWidth, $sourceHeight) = getimagesize($sourcePath);

        $aspectRatio = $sourceWidth / $sourceHeight;
        $targetAspectRatio = $width / $height;

        if ($aspectRatio > $targetAspectRatio) {
            $newWidth = $width;
            $newHeight = $width / $aspectRatio;
        } else {
            $newWidth = $height * $aspectRatio;
            $newHeight = $height;
        }

        $image = imagecreatefromstring(file_get_contents($sourcePath));
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

        imagejpeg($resizedImage, $destinationPath);

        imagedestroy($image);
        imagedestroy($resizedImage);
    }
}
