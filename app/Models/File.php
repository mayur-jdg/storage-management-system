<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'folder_id', 'name', 'path', 'size'];

    // Relationship with Folder
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}