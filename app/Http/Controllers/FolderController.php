<?php

namespace App\Http\Controllers;

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
        // $request->validate([
        //     'name' => 'required|string|max:255',
        // ]);

        // // Create a new folder
        // Folder::create([
        //     'name' => $request->name,
        //     'user_id' => Auth::id(),
        // ]);

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
        if ($folder->user_id !== Auth::id()) {
            abort(403); // Forbidden if the folder doesn't belong to the user
        }


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

}