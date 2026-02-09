<?php

namespace App\Policies;

use App\Asset;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('assets.view');
    }

    public function view(User $user, Asset $asset)
    {
        // Teachers can view their assigned assets
        if ($user->hasRole('Teacher')) {
            $teacher = $user->teacher;
            if ($teacher && $asset->assigned_type === 'teacher' && $asset->assigned_id === $teacher->id) {
                return true;
            }
        }

        return $user->hasPermissionTo('assets.view');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('assets.create');
    }

    public function update(User $user, Asset $asset)
    {
        if ($asset->isDisposed()) {
            return false;
        }

        return $user->hasPermissionTo('assets.edit');
    }

    public function delete(User $user, Asset $asset)
    {
        if ($asset->isDisposed()) {
            return false;
        }

        return $user->hasPermissionTo('assets.delete');
    }

    public function assign(User $user, Asset $asset)
    {
        if (!$asset->canBeAssigned()) {
            return false;
        }

        return $user->hasPermissionTo('assets.assign');
    }

    public function dispose(User $user, Asset $asset)
    {
        if ($asset->isDisposed()) {
            return false;
        }

        return $user->hasPermissionTo('assets.dispose');
    }

    public function calculateDepreciation(User $user)
    {
        return $user->hasPermissionTo('asset-depreciation.calculate');
    }

    public function postDepreciation(User $user)
    {
        return $user->hasPermissionTo('asset-depreciation.post-to-ledger');
    }

    public function viewReports(User $user)
    {
        return $user->hasPermissionTo('asset-reports.view');
    }

    public function exportReports(User $user)
    {
        return $user->hasPermissionTo('asset-reports.export');
    }
}
