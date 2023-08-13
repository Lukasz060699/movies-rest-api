<?php

namespace App\Http\Controllers;

use App\Models\Movies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Fetch all movies with associated genres
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
     * Store a newly created movie resource in storage.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $data = $request->validate([
            'title' => 'required|string',
            'cover_image' => 'required|image',
            'description' => 'required|string',
            'producion_country' => 'required|string',
            'genres.*' => 'exists:genres,id',
        ]);

        // Handle the uploaded cover image
        $coverFile = $request->file('cover_image');
        $coverPath = 'covers/' . $coverFile->hashName();
        $destinationPath = public_path('storage/' . $coverPath);
        $this->resizeImage($coverFile->getRealPath(), $destinationPath, 500, 500);

        // Create a new movie record in the database
        $movie = Movies::create([
            'title' => $data['title'],
            'cover_image' => $coverPath,
            'description' => $data['description'],
            'producion_country' => $data['producion_country'],
        ]);

        // Sync the associated genres for the movie
        $movie->genres()->sync($data['genres']);

        return response()->json($movie, 201);
    }

    /**
     * Display the specified movie resource.
     *
     * @param string $id The ID of the movie to be displayed.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        // Find the movie with the specified ID, along with its associated genres, or throw an exception
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
     *
     * @param  Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        // Find the movie by ID
        $movie = Movies::findOrFail($id);

        // Validate user input
        $data = $request->validate([
            'title' => 'string',
            'cover_image' => 'image',
            'description' => 'string',
            'producion_country' => 'string',
            'genres' => 'array',
            'genres.*' => 'exists:genres,id',
        ]);

        // Handle cover image update if provided
         if ($request->hasFile('cover_image')) {

            // Delete old cover image
            Storage::disk('public')->delete($movie->cover_image);

            // Resize and save new cover image
            $coverFile = $request->file('cover_image');
            $coverPath = 'covers/' . $coverFile->hashName();
            $destinationPath = public_path('storage/' . $coverPath);
            $this->resizeImage($coverFile->getRealPath(), $destinationPath, 500, 500);
            $data['cover_image'] = $coverPath;

            // Update cover image path in the movie record
            $movie->update([
                'cover_image' => $data['cover_image'],
            ]);
        }

        // Update other fields of the movie record
        $movie->update([
            'title' => $data['title'] ?? $movie->title,
            'description' => $data['description'] ?? $movie->description,
            'producion_country' => $data['producion_country'] ?? $movie->producion_country,
        ]);

        // Update genres if provided
        if (isset($data['genres'])) {
            $movie->genres()->sync($data['genres']);
        }

        // Return a response indicating successful update
        return response()->json($movie);
    }

    /**
     * Remove the specified movie resource from storage.
     *
     * @param string $id The ID of the movie to be deleted.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        // Find the movie with the specified ID or throw an exception
        $movie = Movies::findOrFail($id);

        // Delete all ratings associated with the movie
        $movie->ratings()->delete();

        // Detach all genres associated with the movie
        $movie->genres()->detach();

        // Delete the cover image file from the public storage
        Storage::disk('public')->delete($movie->cover_image);

        // Delete the movie record from the database
        $movie->delete();
        return response()->json(null, 204);
    }

    /**
     * Perform a search for movies by title.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByTitle(Request $request)
    {
        // Get the title parameter from the request
        $title = $request->input('title');

        // Search for movies with matching title and retrieve associated genres
        $movies = Movies::with('genres')
            ->where('title', 'like', '%' . $title . '%')
            ->get();

        return response()->json($movies);
    }

    /**
     * Resize and save an image to the specified dimensions.
     *
     * @param string $sourcePath Path to the source image file.
     * @param string $destPath Path to save the resized image.
     * @param int $newWidth New width of the resized image.
     * @param int $newHeight New height of the resized image.
     * @throws \Exception When an unsupported image type is encountered.
     */
    private function resizeImage($sourcePath, $destPath, $newWidth, $newHeight) {

        // Get the original width and height of the source image
        list($width, $height) = getimagesize($sourcePath);
    
        // Create a new truecolor image with the specified dimensions
        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
    
        // Determine the image type of the source image
        $imageType = exif_imagetype($sourcePath);
    
        // Process the image based on its type
        switch ($imageType) {
            case IMAGETYPE_JPEG:

                 // Create an image resource from the JPEG source
                $source = imagecreatefromjpeg($sourcePath);

                // Copy and resample the image to the thumbnail with new dimensions
                imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                // Save the resized image as a JPEG with 90% quality
                imagejpeg($thumbnail, $destPath, 90);
                break;
            
            case IMAGETYPE_PNG:

                // Configure PNG settings for proper alpha channel handling
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);

                // Create a transparent color for filling
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);

                // Fill the thumbnail with the transparent color
                imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);

                // Create an image resource from the PNG source
                $source = imagecreatefrompng($sourcePath);

                 // Copy and resample the image to the thumbnail with new dimensions
                imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                // Save the resized image as a PNG
                imagepng($thumbnail, $destPath);
                break;
    
            default:
                // Throw an exception for unsupported image types
                throw new \Exception("Unsupported image type: " . $imageType);
        }
    
        // Destroy the image resources to free memory
        imagedestroy($thumbnail);
        imagedestroy($source);
    }
    
}
