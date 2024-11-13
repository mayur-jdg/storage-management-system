<?php

use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(LoginRegisterController::class)->group(function() {
    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/files', 'home')->name('home');
    Route::post('/logout', 'logout')->name('logout');
});

Route::get('/folders/create', [FolderController::class, 'create'])->name('folders.create');
Route::get('/folders', [FolderController::class, 'index'])->name('folders.index');
Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
Route::get('/folders/{folder}', [FolderController::class, 'show'])->name('folders.show');


Route::get('/file', [FileController::class, 'index'])->name('files.index');
Route::post('/files/upload', [FileController::class, 'upload'])->name('files.upload');
Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');

Route::get('/filess/{file}', [FileController::class, 'showFileDetails']);