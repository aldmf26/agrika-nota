<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Nota;

class NotaPolicy
{
    /**
     * Determine whether the user can view any notas.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['nota.view-own', 'nota.view-all']);
    }

    /**
     * Determine whether the user can view the nota.
     */
    public function view(User $user, Nota $nota): bool
    {
        // Super admin atau approver bisa lihat semua
        if ($user->hasPermissionTo('nota.view-all')) {
            return true;
        }

        // Admin hanya bisa lihat nota milik sendiri
        if ($user->hasPermissionTo('nota.view-own')) {
            return $nota->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create notas.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('nota.create');
    }

    /**
     * Determine whether the user can update the nota.
     */
    public function update(User $user, Nota $nota): bool
    {
        // Hanya bisa edit nota dengan status REJECTED (untuk revisi)
        // Pending yang baru di-auto-submit TIDAK bisa diedit
        // Approved = locked, tidak bisa edit
        if ($nota->status !== 'rejected') {
            return false;
        }

        // Pemilik nota bisa edit
        if ($user->hasPermissionTo('nota.edit-own') && $nota->user_id === $user->id) {
            return true;
        }

        // Super admin bisa edit semua
        if ($user->hasPermissionTo('nota.edit-all')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the nota.
     */
    public function delete(User $user, Nota $nota): bool
    {
        // Hanya status pending/rejected yang bisa didelete
        // Approved & void tidak boleh delete
        if (!in_array($nota->status, ['pending', 'rejected'])) {
            return false;
        }

        return $nota->user_id === $user->id;
    }

    /**
     * Determine whether the user can approve the nota.
     */
    public function approve(User $user, Nota $nota): bool
    {
        return $user->hasPermissionTo('nota.approve') && $nota->status === 'pending';
    }

    /**
     * Determine whether the user can reject the nota.
     */
    public function reject(User $user, Nota $nota): bool
    {
        return $user->hasPermissionTo('nota.reject') && $nota->status === 'pending';
    }

    /**
     * Determine whether the user can void the nota.
     */
    public function void(User $user, Nota $nota): bool
    {
        return $user->hasPermissionTo('nota.void') && in_array($nota->status, ['approved', 'rejected']);
    }

    /**
     * Determine whether the user can export notas.
     */
    public function export(User $user): bool
    {
        return $user->hasPermissionTo('nota.export');
    }
}
