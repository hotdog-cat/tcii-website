<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_EDITOR = 'editor';
    public const ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_EDITOR => 'Editor',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
        'profile_photo_path',
        'contact_number',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_admin' => 'boolean'];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->is_admin;
    }

    public function isEditor(): bool
    {
        return $this->role === self::ROLE_EDITOR && ! $this->isAdmin();
    }

    public function hasAdminAccess(): bool
    {
        return $this->isAdmin() || $this->role === self::ROLE_EDITOR;
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? ($this->is_admin ? 'Admin' : 'Editor');
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (! $this->profile_photo_path) {
            return null;
        }

        if (str_starts_with($this->profile_photo_path, 'http')) {
            return $this->profile_photo_path;
        }

        return asset('storage/'.$this->profile_photo_path);
    }
}
