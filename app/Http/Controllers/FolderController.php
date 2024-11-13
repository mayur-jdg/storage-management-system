<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search'); // Get search query from the request
        $folders = Folder::where('user_id', Auth::id()) // Filter by the authenticated user's ID
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('name', 'like', "%{$query}%"); // Filter folders by name
            })
            ->get();
        // Fetch folders for the logged-in user
        // $folders = Folder::where('user_id', Auth::id())->get();
        return view('folders.index', compact('folders'));
    }

    public function create()
    {
        return view('folders.create'); // Make sure to create this view
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id', // Validate that parent_id is valid
        ]);

        // Create a new folder with an optional parent_id
        $folder = Folder::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
        ]);

        // Return folder data as JSON
        return response()->json($folder);
    }

    public function show(Folder $folder, Request $request)
    {
        $query = $request->input('search');
        // Ensure the folder belongs to the logged-in user
        // if ($folder->user_id !== Auth::id()) {
        //     abort(403); // Forbidden if the folder doesn't belong to the user
        // }

        // Get the files inside the folder (assuming you have a 'files' relationship)
        $files = $folder->files; // Assuming there's a 'files' relationship on the Folder model
         // Get child folders where the parent_id matches the current folder's id
        $childFolders = Folder::where('parent_id', $folder->id)
        ->when($query, function ($queryBuilder) use ($query) {
            return $queryBuilder->where('name', 'like', "%{$query}%"); // Filter folders by name
        })
        ->get();

        return view('folders.show', compact('folder', 'files', 'childFolders'));
    }

    public function upload(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'folder_names' => 'required|string',  // Expecting folder names as a JSON string
            'files' => 'required|array',
            'files.*' => 'file',
        ]);

        // Decode the folder names from JSON
        $folderNames = json_decode($request->input('folder_names'), true);

        // Ensure that files are included in the request
        if ($request->hasFile('files')) {
            $files = $request->file('files');

            // Loop through the folder names and save them
            foreach ($folderNames as $folderName) {
                // Create or find each folder in the database
                $folder = Folder::firstOrCreate([
                    'name' => $folderName,
                    'user_id' => Auth::id(),
                    'parent_id' => null // Set to null for root-level folders, you can adjust as needed
                ]);

                // Loop through the files and associate each one with the current folder
                foreach ($files as $file) {
                    // Store the file
                    $filePath = $file->store('uploads', 'public');
                    $fileName = $file->getClientOriginalName();

                    // Create the file record and associate it with the folder
                    File::create([
                        'name' => $fileName,
                        'path' => $filePath,
                        'size' => $file->getSize(),
                        'user_id' => Auth::id(),
                        'folder_id' => $folder->id // Link the file to the correct folder
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Folders and files uploaded successfully!']);
    }




}
