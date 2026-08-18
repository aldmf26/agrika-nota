<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'all_divisi',
    ];

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
            'all_divisi' => 'boolean',
        ];
    }

    public function divisis()
    {
        return $this->belongsToMany(Divisi::class, 'divisi_user', 'user_id', 'divisi_id');
    }

    /**
     * Dapatkan daftar divisi yang dapat diakses oleh user ini.
     */
    public function accessibleDivisis()
    {
        if ($this->hasRole('super_admin') || $this->hasRole('approver') || $this->all_divisi || $this->all_divisi === null) {
            return Divisi::aktif()->get();
        }

        $assigned = $this->divisis()->where('aktif', true)->get();

        if ($assigned->isEmpty()) {
            return Divisi::aktif()->get();
        }

        return $assigned;
    }

    /**
     * Cek apakah user berhak mengakses divisi tertentu.
     */
    public function canAccessDivisi($divisiId): bool
    {
        if (! $divisiId) {
            return true;
        }

        if ($this->hasRole('super_admin') || $this->hasRole('approver') || $this->all_divisi || $this->all_divisi === null) {
            return true;
        }

        if ($this->divisis()->count() === 0) {
            return true;
        }

        return $this->divisis()->where('divisis.id', $divisiId)->exists();
    }
}
