<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Get the users that have this role
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the permissions for this role
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions->contains('name', $permission);
        }

        return $this->permissions->contains($permission);
    }

    /**
     * Assign permission to role
     */
    public function givePermissionTo($permission)
    {
        return $this->permissions()->attach($permission);
    }

    /**
     * Remove permission from role
     */
    public function revokePermissionTo($permission)
    {
        return $this->permissions()->detach($permission);
    }
}
