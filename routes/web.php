<?php

use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\TrashController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(LoginRegisterController::class)->group(function() {
    Route::get('/register', 'register')->name('register');
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');

    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware('auth')->group(function() {

    Route::get('/files', [LoginRegisterController::class,'home'])->name('home');
    Route::post('/store', [LoginRegisterController::class,'store'])->name('store');

    Route::get('/folders/create', [FolderController::class, 'create'])->name('folders.create');
    Route::get('/folders', [FolderController::class, 'index'])->name('folders.index');
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::get('/folders/{folder}', [FolderController::class, 'show'])->name('folders.show');

    Route::post('/folders/upload', [FolderController::class, 'upload'])->name('folders.upload');

    Route::get('/file', [FileController::class, 'index'])->name('files.index');
    Route::post('/files/upload', [FileController::class, 'upload'])->name('files.upload');
    Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');
    Route::post('/files/upload-to-folder', [FileController::class, 'uploadToFolder'])->name('files.uploadToFolder');


    Route::get('/filess/{file}', [FileController::class, 'showFileDetails']);
    Route::get('/filess/{id}', [FileController::class, 'showFileDetailss'])->name('files.details');

    Route::get('/trash', [TrashController::class, 'index'])->name('trash');
});