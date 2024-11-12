<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Quest;
use Illuminate\Auth\Access\Response;

class QuestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin $admin, Quest $quest): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin $admin): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin $admin, Quest $quest): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin $admin, Quest $quest): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Admin $admin, Quest $quest): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Admin $admin, Quest $quest): bool
    {
        //
    }
}
