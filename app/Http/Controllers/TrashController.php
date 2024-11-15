<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TrashController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->input('search'); // Get search query from the request

        //where('user_id', Auth::id()) // Filter by the authenticated user's ID
        //whereNull('parent_id')->
        $folders = Folder::where('status', 1)
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('name', 'like', "%{$query}%"); // Filter folders by name
            })
            ->get();

        // Fetch files ->where('user_id', Auth::id()) // Filter by the authenticated user's ID
        $files = File::where('status', 1)
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('name', 'like', "%{$query}%"); // Filter files by name
            })
            ->get();


        return view('auth.trash', compact('folders', 'files'));
    }

    public function deleteItems(Request $request)
    {
        $folderIds = $request->input('folders', []);
        $fileIds = $request->input('files', []);

        // Update status to 1 for selected folders
        if (!empty($folderIds)) {
            Folder::whereIn('id', $folderIds)->update(['status' => 1]);
        }

        // Update status to 1 for selected files
        if (!empty($fileIds)) {
            File::whereIn('id', $fileIds)->update(['status' => 1]);
        }

        return response()->json(['success' => true]);
    }

    public function restoreItems(Request $request)
    {
        $folderIds = $request->input('folders', []);
        $fileIds = $request->input('files', []);

        // Update status to 1 for selected folders
        if (!empty($folderIds)) {
            Folder::whereIn('id', $folderIds)->update(['status' => 0]);
        }

        // Update status to 1 for selected files
        if (!empty($fileIds)) {
            File::whereIn('id', $fileIds)->update(['status' => 0]);
        }

        return response()->json(['success' => true]);
    }

    public function permanentlyDelete(Request $request)
    {
        $folderIds = $request->input('selectedFolders', []);
        $fileIds = $request->input('selectedFiles', []);

        DB::transaction(function () use ($folderIds, $fileIds) {
            // Delete files from storage and database
            if (!empty($fileIds)) {
                $files = File::whereIn('id', $fileIds)->get();
                foreach ($files as $file) {
                    // Delete the file from storage
                    Storage::disk('public')->delete($file->path);

                    // Delete the file record from the database
                    $file->delete();
                }
            }

            // Delete folders and their nested subfolders/files recursively
            if (!empty($folderIds)) {
                foreach ($folderIds as $folderId) {
                    $this->deleteFolderRecursively($folderId);
                }
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Selected items permanently deleted.']);
    }

    // Helper function to delete folder and nested contents
    private function deleteFolderRecursively($folderId)
    {
        $folder = Folder::findOrFail($folderId);

        // Delete all files in the folder
        $files = File::where('folder_id', $folder->id)->get();
        foreach ($files as $file) {
            // Delete the file from storage
            Storage::disk('public')->delete($file->path);

            // Delete the file record from the database
            $file->delete();
        }

        // Delete all subfolders recursively
        $subFolders = Folder::where('parent_id', $folder->id)->get();
        foreach ($subFolders as $subFolder) {
            $this->deleteFolderRecursively($subFolder->id);
        }

        // Finally, delete the folder itself
        $folder->delete();
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
        //
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
        //
    }
}
