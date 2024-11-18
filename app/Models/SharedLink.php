<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedLink extends Model
{
    protected $fillable = ['token', 'folder_ids', 'file_ids'];
}