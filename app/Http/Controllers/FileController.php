<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
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

    public function uploadToFolder(Request $request)
    {
        // Validate the request, including the folder_id
        $request->validate([
            'folder_id' => 'required|exists:folders,id', // Check that folder_id exists
            'files' => 'required|array',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:100240', // Max size 10MB per file
        ]);

        $folderId = $request->input('folder_id');
        $uploadedFiles = [];

        // Loop through each file and store it
        foreach ($request->file('files') as $file) {
            $path = $file->store('uploads', 'public');

            // Create a new record in the database for each uploaded file
            $newFile = File::create([
                'user_id' => Auth::id(),
                'folder_id' => $folderId,
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
            ]);

            $uploadedFiles[] = $newFile; // Add the file to the array of uploaded files
        }

        return response()->json($uploadedFiles);
    }

    public function show(Folder $folder, Request $request)
    {
        $query = $request->input('search');

        // Ensure the folder belongs to the logged-in user
        if ($folder->user_id !== Auth::id()) {
            abort(403); // Forbidden if the folder doesn't belong to the user
        }

        // Get the files inside the folder and filter by name if needed
        $files = $folder->files()
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('name', 'like', "%{$query}%"); // Filter files by name
            })
            ->get();

        // Get child folders where the parent_id matches the current folder's id
        $childFolders = Folder::where('parent_id', $folder->id)
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('name', 'like', "%{$query}%"); // Filter folders by name
            })
            ->get();

        return view('folders.show', compact('folder', 'files', 'childFolders'));
    }

    public function showFileDetails(File $file)
    {
        // if ($file->user_id !== Auth::id()) {
        //     abort(403); // Forbidden access
        // }

        $filePath = asset('storage/' . $file->path); // Get the public URL
        $fileExtension = pathinfo($file->path, PATHINFO_EXTENSION); // Get the file extension

        return response()->json([
            'file_url' => $filePath,
            'file_name' => $file->name,
            'file_extension' => $fileExtension,
        ]);
    }

    // FileController.php
    public function showFileDetailss($id)
    {
        $file = File::findOrFail($id);

        return response()->json([
            'file_url' => asset('storage/' . $file->path),
            'file_name' => $file->name,
            'file_extension' => pathinfo($file->path, PATHINFO_EXTENSION),
        ]);
    }
}
