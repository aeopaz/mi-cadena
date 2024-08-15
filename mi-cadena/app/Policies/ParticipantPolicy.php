<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\SavingChain\Participant;
use App\Models\User;

class ParticipantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Participant $participant): bool
    {
        //
    }

    public function decision_invitation(User $user, Participant $participant): bool
    {
        return $participant->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Participant $participant): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Participant $participant): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Participant $participant): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Participant $participant): bool
    {
        //
    }
}
