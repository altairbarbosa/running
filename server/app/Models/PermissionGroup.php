<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    protected $fillable = ['name', 'key', 'description', 'is_system'];

    protected function casts(): array
    {
        return ['is_system' => 'boolean'];
    }

    public function permissions()
    {
        return $this->hasMany(PermissionGroupPermission::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
