<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['key_name','label'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission')
            ->withPivot('allowed')
            ->withTimestamps();
    }
}
