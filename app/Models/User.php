<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'role', 'permissions', 'disabled_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'is_admin' => 'boolean',
            'permissions' => 'array',
            'disabled_at' => 'datetime',
        ];
    }

    public function isMainAdmin(): bool
    {
        return $this->is_admin && $this->role === 'main';
    }

    public function isSubAdmin(): bool
    {
        return $this->is_admin && $this->role === 'sub';
    }

    public function isDisabled(): bool
    {
        return $this->disabled_at !== null;
    }

    /**
     * Main Admin always has full access. A Sub Admin only has whatever
     * actions the Main Admin granted them for that section.
     */
    public function hasPermission(string $section, string $action = 'view'): bool
    {
        if ($this->isMainAdmin()) {
            return true;
        }

        return in_array($action, $this->permissions[$section] ?? [], true);
    }

    /** True if this user has ANY permission at all — used for generic "can enter the admin panel" checks. */
    public function hasAnyPermission(): bool
    {
        if ($this->isMainAdmin()) {
            return true;
        }

        return collect($this->permissions ?? [])->flatten()->isNotEmpty();
    }

    /** True if this user was granted at least one action on this section — used to show/hide nav links. */
    public function hasSectionAccess(string $section): bool
    {
        if ($this->isMainAdmin()) {
            return true;
        }

        return filled($this->permissions[$section] ?? null);
    }
}
