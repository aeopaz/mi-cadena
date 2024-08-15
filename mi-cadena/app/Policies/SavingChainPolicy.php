<?php

namespace App\Policies;

use App\Models\SavingChain\Participant;
use Illuminate\Auth\Access\Response;
use App\Models\SavingChain\SavingChain;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SavingChainPolicy
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
    public function view(User $user, SavingChain $saving_chain): bool
    {
        return ($user->id ==  $saving_chain->creator_id) ||  ($saving_chain->participants->where('user_id', $user->id)->count() > 0);
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
    public function update(User $user, SavingChain $saving_chain): bool
    {
        return $user->id ==  $saving_chain->creator_id;
    }

   

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SavingChain $saving_chain): bool
    {
        return $user->id ==  $saving_chain->creator_id;
    }
}
