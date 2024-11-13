<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function index()
    {
        $files = File::where('user_id', Auth::id())->get();
        return view('files.index', compact('files'));
    }

    public function upload(Request $request)
    {
        // $request->validate([
        //     'file' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:100240', // Max size 10MB
        // ]);

        // $file = $request->file('file');
        // $path = $file->store('uploads', 'public');

        // $newFile = File::create([
        //     'user_id' => Auth::id(),
        //     'name' => $file->getClientOriginalName(),
        //     'path' => $path,
        //     'size' => $file->getSize(),
        // ]);

        // return response()->json($newFile);

        // Validate the uploaded files
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:100240', // Max size 10MB per file
        ]);

        $uploadedFiles = [];

        // Loop through each file and store it
        foreach ($request->file('files') as $file) {
            $path = $file->store('uploads', 'public');

            // Create a new record in the database for each uploaded file
            $newFile = File::create([
                'user_id' => Auth::id(),
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
            ]);

            $uploadedFiles[] = $newFile; // Add the file to the array of uploaded files
        }

        return response()->json($uploadedFiles);
    }

    public function show(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403); // Forbidden
        }

        return response()->download(storage_path('app/public/' . $file->path));
    }

    public function showFileDetails(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403); // Forbidden access
        }

        $filePath = asset('storage/' . $file->path); // Get the public URL
        $fileExtension = pathinfo($file->path, PATHINFO_EXTENSION); // Get the file extension

        return response()->json([
            'file_url' => $filePath,
            'file_name' => $file->name,
            'file_extension' => $fileExtension,
        ]);
    }
}
