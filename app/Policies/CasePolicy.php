<?php

namespace App\Policies;

use App\Models\CaseModel;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CasePolicy
{
    use HandlesAuthorization;

    public function moveToUnderReview(User $user, CaseModel $case)
    {
        return $user->canReview() && $case->status === 'new';
    }

    public function markAsDocumented(User $user, CaseModel $case)
    {
        return $user->canReview() && $case->status === 'under_review';
    }

    public function archive(User $user, CaseModel $case)
    {
        // هل توجد إحالة مرتبطة بهذه الحالة؟
        $hasReferral = \DB::connection('cases')
            ->table('case_entity_referrals')
            ->where('case_id', $case->id)
            ->exists();

        return $user->canReview()
            && in_array($case->status, ['under_review', 'documented'])
            && !$hasReferral;
    }

}

