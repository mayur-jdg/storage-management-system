<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

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
            ->where('status', 0)
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('name', 'like', "%{$query}%"); // Filter files by name
            })
            ->get();

        // Get child folders where the parent_id matches the current folder's id
        $childFolders = Folder::where('parent_id', $folder->id)
        ->where('status', 0)
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

    public function showFileDetailss($id)
    {
        $file = File::findOrFail($id);

        return response()->json([
            'file_url' => asset('storage/' . $file->path),
            'file_name' => $file->name,
            'file_extension' => pathinfo($file->path, PATHINFO_EXTENSION),
        ]);
    }
    
    public function downloadFilesOrFolders(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'file_ids' => 'nullable|array',
            'file_ids.*' => 'exists:files,id', // Validate file IDs
            'folder_ids' => 'nullable|array',
            'folder_ids.*' => 'exists:folders,id', // Validate folder IDs
        ]);

        $zip = new ZipArchive;
        $zipFileName = 'download_items_' . time() . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            // Process selected files
            if ($request->has('file_ids')) {
                $files = File::whereIn('id', $request->input('file_ids'))->get();
                foreach ($files as $file) {
                    $filePath = storage_path('app/public/' . $file->path);
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, $file->name);
                    }
                }
            }

            // Process selected folders
            if ($request->has('folder_ids')) {
                $folders = Folder::whereIn('id', $request->input('folder_ids'))->get();
                foreach ($folders as $folder) {
                    // Add folder to the zip
                    $zip->addEmptyDir($folder->name);
                    $this->addFolderToZip($zip, $folder);
                }
            }

            // Close the zip file
            $zip->close();

            // Return the URL for the download
            return response()->json([
                'success' => true,
                'download_url' => asset('storage/' . $zipFileName)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Could not create the zip file.'
            ]);
        }
    }

    // Helper function to recursively add folders and files to the zip
    private function addFolderToZip($zip, $folder, $parentFolderName = '')
    {
        $folderPath = $parentFolderName ? $parentFolderName . '/' . $folder->name : $folder->name;

        // Add files in the folder to the zip
        $files = File::where('folder_id', $folder->id)->get();
        foreach ($files as $file) {
            $filePath = storage_path('app/public/' . $file->path);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $folderPath . '/' . $file->name);
            }
        }

        // Recursively add subfolders
        $subfolders = Folder::where('parent_id', $folder->id)->get();
        foreach ($subfolders as $subfolder) {
            $this->addFolderToZip($zip, $subfolder, $folderPath);
        }
    }
}