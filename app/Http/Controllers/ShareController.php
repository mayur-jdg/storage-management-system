<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShareController extends Controller
{
    // Generate a shareable link for selected items
    public function generateShareLink(Request $request)
    {
        $request->validate([
            'file_ids' => 'nullable|array',
            'file_ids.*' => 'exists:files,id',
            'folder_ids' => 'nullable|array',
            'folder_ids.*' => 'exists:folders,id',
        ]);

        // Generate a unique token for the shared link
        $token = Str::random(32);

        // Store the token with the selected items in the database
        // You can store the information in a database table (like `shared_items`) or cache it for a temporary period.
        \Cache::put('shared_link_' . $token, [
            'file_ids' => $request->input('file_ids', []),
            'folder_ids' => $request->input('folder_ids', []),
        ], now()->addHours(24));  // Link expires in 24 hours

        // Return the generated URL to the client
        return response()->json([
            'success' => true,
            'url' => route('share.view', ['token' => $token])
        ]);
    }

    // View shared items (redirect to login if not logged in)
    public function viewSharedItems($token, Request $request)
    {
        $sharedItems = \Cache::get('shared_link_' . $token);

        // If the items do not exist or the link expired, show an error
        if (!$sharedItems) {
            return abort(404, 'This link is expired or invalid.');
        }

        // Check if the user is logged in
        if (Auth::check()) {
            // If logged in, show the shared items
            $files = File::whereIn('id', $sharedItems['file_ids'])->get();
            $folders = Folder::whereIn('id', $sharedItems['folder_ids'])->get();

            return view('shared_items', compact('files', 'folders'));
        } else {
            // If not logged in, redirect to login with the current URL to resume after login
            return redirect()->route('login', ['redirect_url' => url()->full()]);
        }
    }
}