<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function favorites()
    {
        return $this->hasMany(\App\Models\Favorite::class);
    }

    public function favoriteFiles()
    {
        return $this->belongsToMany(\App\Models\File::class, 'favorites')->withTimestamps();
    }

    // ...
    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class, 'user_roles')->withTimestamps();
    }

    public function permissions() // via role_permission
    {
        return \App\Models\Permission::query()
            ->select('permissions.*')
            ->join('role_permission','role_permission.permission_id','=','permissions.id')
            ->join('user_roles','user_roles.role_id','=','role_permission.role_id')
            ->where('user_roles.user_id', $this->id)
            ->where('role_permission.allowed', true);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function canKey(string $permissionKey): bool
    {
        return $this->permissions()->where('permissions.key_name', $permissionKey)->exists();
    }


}
