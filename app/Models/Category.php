<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    // Relasi dengan model File
    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function folders()
    {
    return $this->hasMany(Folder::class);
    }

}
