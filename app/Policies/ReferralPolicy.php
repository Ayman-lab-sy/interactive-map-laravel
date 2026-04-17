<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CaseModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReferralPolicy
{
    use HandlesAuthorization;

    public function create(User $user, CaseModel $case)
    {
        return $user->canReview() && $case->status === 'documented';
    }
}
