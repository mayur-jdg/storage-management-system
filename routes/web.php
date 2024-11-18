<?php

use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\TrashController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(LoginRegisterController::class)->group(function() {
    Route::get('/register', 'register')->name('register');
    Route::get('/login', 'login')->name('login');
    Route::post('/store', [LoginRegisterController::class,'store'])->name('store');
    Route::post('/authenticate', 'authenticate')->name('authenticate');

    Route::post('/logout', 'logout')->name('logout');
});

Route::get('/controller', function () {
    // $exitCode = Artisan::call('migrate');
    Artisan::call('make:controller test');
});

Route::get('/migrate', function () {
    Artisan::call('migrate');
     Artisan::call('db:seed');
});

Route::middleware('auth')->group(function() {

    Route::get('/files', [LoginRegisterController::class,'home'])->name('home');

    Route::get('/folders/create', [FolderController::class, 'create'])->name('folders.create');
    Route::get('/folders', [FolderController::class, 'index'])->name('folders.index');
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::get('/folders/{folder}', [FolderController::class, 'show'])->name('folders.show');

    Route::post('/folders/upload', [FolderController::class, 'upload'])->name('folders.upload');
    Route::post('/folders/upload-to-folder', [FolderController::class, 'uploadToFolder'])->name('folders.uploadToFolder');

    Route::get('/file', [FileController::class, 'index'])->name('files.index');
    Route::post('/files/upload', [FileController::class, 'upload'])->name('files.upload');
    Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');
    Route::post('/files/upload-to-folder', [FileController::class, 'uploadToFolder'])->name('files.uploadToFolder');


    Route::get('/filess/{file}', [FileController::class, 'showFileDetails']);
    Route::get('/filess/{id}', [FileController::class, 'showFileDetailss'])->name('files.details');

    Route::post('/files-or-folders/download', [FileController::class, 'downloadFilesOrFolders'])->name('filesOrFolders.download');

    Route::get('/share/{token}', [ShareController::class, 'viewSharedItems'])->name('share.view');
    Route::post('/generate-shareable-link', [ShareController::class, 'generateShareableLink']);


    Route::post('/delete-items', [TrashController::class, 'deleteItems'])->name('delete.items');
    Route::post('/restore-items', [TrashController::class, 'restoreItems'])->name('restore.items');
    Route::post('/folders/permanently-delete', [TrashController::class, 'permanentlyDelete'])->name('folders.permanentlyDelete');


    Route::get('/trash', [TrashController::class, 'index'])->name('trash');
});