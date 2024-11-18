<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Folder;
use App\Models\SharedLink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShareController extends Controller
{
    public function viewSharedItems(Request $request, $token)
    {
        // Find the shared link by the token
        $sharedLink = SharedLink::where('token', $token)->first();

        // If no shared link is found, return a 404 response
        if (!$sharedLink) {
            abort(404, 'Shared link not found.');
        }

        // Parse folder and file IDs from the shared link
        $folderIds = explode(',', $sharedLink->folder_ids);
        $fileIds = explode(',', $sharedLink->file_ids);

        // Fetch folders and files from the database
        $folders = Folder::whereIn('id', $folderIds)->where('status', 0)->get();
        $files = File::whereIn('id', $fileIds)->where('status', 0)->get();

        // Return the view with the selected folders and files
        return view('auth.home', compact('folders', 'files'));
    }

    public function generateShareableLink(Request $request)
    {
        try {
            // Collect selected folder and file IDs
            $folderIds = $request->input('folders', []);
            $fileIds = $request->input('files', []);

            // Generate a unique token
            $token = Str::random(16);

            // Store the token and associated folder and file IDs in the shared_links table
            SharedLink::create([
                'token' => $token,
                'folder_ids' => implode(',', $folderIds),
                'file_ids' => implode(',', $fileIds),
            ]);

            // Generate the shareable link
            $shareLink = url('/share') . "/{$token}";

            // Return the link as a JSON response
            return response()->json(['link' => $shareLink]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error($e->getMessage());
            return response()->json(['error' => 'An error occurred while generating the shareable link.'], 500);
        }
    }
}