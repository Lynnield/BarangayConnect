<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function givePermission(string $slug): void
    {
        $permission = Permission::where('slug', $slug)->first();
        if ($permission) {
            $this->permissions()->syncWithoutDetaching($permission->id);
        }
    }

    public function revokePermission(string $slug): void
    {
        $permission = Permission::where('slug', $slug)->first();
        if ($permission) {
            $this->permissions()->detach($permission->id);
        }
    }
}
