<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ImageController extends Controller
{
    public function showImage($filename)
    {

        dd($filename);
        try {
            // Define the path to the image inside storage (change the folder as needed)
            $path = storage_path('app/private/images/' . $filename); // Adjust this path based on your storage location

            // Check if the file exists
            if (!file_exists($path)) {
                throw new FileNotFoundException("The image does not exist.");
            }

            // Return the image as a response with the correct MIME type
            return Response::file($path, [
                'Content-Type' => mime_content_type($path),
            ]);
        } catch (FileNotFoundException $e) {
            return response()->json(['error' => 'Image not found'], 404);
        }
    }
}