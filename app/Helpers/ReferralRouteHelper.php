<?php

namespace App\Helpers;

class ReferralRouteHelper
{
    public static function resolve($ref)
    {
        if (!$ref) {
            return route('admin.referrals.index');
        }

        $referralId = $ref->referral_id ?? $ref->id ?? null;

        if (!$referralId) {
            return route('admin.referrals.index');
        }

        $track = $ref->referral_track ?? null;

        switch ($track) {

            case 'SPECIAL_PROCEDURES':
                return match ($ref->special_procedure_type ?? null) {
                    'TORTURE' => route('admin.referrals.unsp.torture.show', $referralId),
                    'ENFORCED_DISAPPEARANCE' => route('admin.referrals.un_sp.enforced_disappearance.show', $referralId),
                    'ARBITRARY_DETENTION' => route('admin.referrals.un_sp.arbitrary_detention.show', $referralId),
                    'FREEDOM_OF_EXPRESSION' => route('admin.referrals.un_sp.freedom_expression.show', $referralId),
                    'HUMAN_RIGHTS_DEFENDERS' => route('admin.referrals.un_sp.human_rights_defenders.show', $referralId),
                    'EXTRAJUDICIAL_EXECUTIONS' => route('admin.referrals.un_sp.extrajudicial_executions.show', $referralId),
                    'VIOLENCE_AGAINST_WOMEN' => route('admin.referrals.un_sp.violence_against_women.show', $referralId),
                    'MINORITY_ISSUES' => route('admin.referrals.un_sp.minority_issues.show', $referralId),
                    'FREEDOM_OF_RELIGION' => route('admin.referrals.un_sp.freedom_religion.show', $referralId),
                    default => route('admin.referrals.show', $referralId),
                };

            case 'HUMANITARIAN_PROTECTION':
                return match ($ref->humanitarian_type ?? null) {
                    'ICRC' => route('admin.referrals.humanitarian.icrc.show', $referralId),
                    'UNHCR' => route('admin.referrals.humanitarian.unhcr.show', $referralId),
                    default => route('admin.referrals.show', $referralId),
                };

            case 'NGO_LEGAL':
                return match ($ref->ngo_type ?? null) {
                    'AMNESTY' => route('admin.referrals.amnesty.show', $referralId),
                    'HRW'     => route('admin.referrals.ngo.hrw.show', $referralId),
                    default   => route('admin.referrals.show', $referralId),
                };


            case 'UN_ACCOUNTABILITY':
                return match ($ref->un_accountability_type ?? null) {
                    'OHCHR' => route('admin.referrals.un_accountability.ohchr.show', $referralId),
                    default => route('admin.referrals.show', $referralId),
                };

            default:
                return route('admin.referrals.show', $referralId);
        }
    }
}
