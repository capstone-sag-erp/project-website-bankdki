<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_path',
        'category_id',
        'user_id',
        'size',
        'folder_id',
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function folder() { return $this->belongsTo(Folder::class); }

    // Accessor: kembalikan size dalam MB dengan 2 desimal jika ada
    public function getSizeAttribute($value)
    {
        if (is_null($value)) return null;
        // diasumsikan disimpan dalam byte
        if ($value >= 1048576) {
            return number_format($value / 1048576, 2) . ' MB';
        }
        return number_format($value / 1024, 2) . ' KB';
    }
}
