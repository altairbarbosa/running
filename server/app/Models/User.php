<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'permission_group_id', 'phone', 'birth_date', 'address', 'avatar_path', 'active', 'must_change_password', 'password_changed_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'active' => 'boolean',
            'must_change_password' => 'boolean',
            'password_changed_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class, 'member_id');
    }

    public function createdWorkouts()
    {
        return $this->hasMany(Workout::class, 'created_by');
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class, 'member_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'member_id');
    }

    public function permissionGroup()
    {
        return $this->belongsTo(PermissionGroup::class);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $permissions = $this->permissionGroup
            ? $this->permissionGroup->permissions->pluck('permission')->all()
            : config('permissions.defaults.'.$this->role, []);

        if (in_array('*', $permissions, true) || in_array($permission, $permissions, true)) {
            return true;
        }

        $impliedBy = [
            'members.view' => 'members.manage',
            'workouts.view' => 'workouts.manage',
            'shop.view' => 'shop.manage',
        ];

        return isset($impliedBy[$permission]) && in_array($impliedBy[$permission], $permissions, true);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'trainer'], true);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getInitialsAttribute(): string
    {
        return str($this->name)->explode(' ')->filter()->take(2)->map(fn ($part) => str($part)->substr(0, 1))->implode('');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path ? asset('storage/'.$this->avatar_path) : null;
    }
}
