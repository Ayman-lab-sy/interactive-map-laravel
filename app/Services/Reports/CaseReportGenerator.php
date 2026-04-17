<?php

namespace App\Services\Reports;

use App\Models\CaseModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use App\Services\Pdf\GotenbergPdfService;


class CaseReportGenerator
{
    public function generate(
        CaseModel $case,
        int $referralId,
        $user,
        string $language = 'AR',
        bool $includeIdentity = false
    ): array {
        // 0) معاملات أساسية
        $lang = strtoupper($language) === 'EN' ? 'EN' : 'AR';
        // تحميل الصياغة القانونية المعتمدة (Human Legal Narrative)
        $isUN = (($case->referral_track ?? null) === 'SPECIAL_PROCEDURES');
        $isAmnesty = (
            ($case->referral_track ?? null) === 'NGO_LEGAL'
            && ($case->entity_name ?? null) === 'Amnesty International'
        );

        $narrative = null;

        if (!$isUN && !$isAmnesty && $referralId) {
            $narrative = DB::connection('cases')
                ->table('case_referral_narratives')
                ->where('referral_id', $referralId)
                ->where('language', strtolower($lang))
               ->value('content');
        }

        $identityMasked = !$includeIdentity;

        // 1) تحميل البيانات المساندة
        $updatesCount = DB::connection('cases')
            ->table('case_updates')->where('case_id', $case->id)->count();

        $files = DB::connection('cases')
            ->table('case_files')->where('case_id', $case->id)->get();

        // 2) تطبيق إخفاء الهوية (نسخة منقّحة)
        $reportData = $this->anonymize($case, $identityMasked);

        //جلب القيم التحريرية
        $editorial = null;

        if (($case->referral_track ?? null) === 'SPECIAL_PROCEDURES') {

            // UN Special Procedures
            $editorial = match ($case->special_procedure_type ?? null) {
                'TORTURE' =>
                    DB::connection('cases')->table('case_referral_un_sp_torture')->where('referral_id', $referralId)->first(),
                'ENFORCED_DISAPPEARANCE' =>
                    DB::connection('cases')->table('case_referral_un_sp_enforced_disappearance')->where('referral_id', $referralId)->first(),
                'ARBITRARY_DETENTION' =>
                    DB::connection('cases')->table('case_referral_un_sp_arbitrary_detention')->where('referral_id', $referralId)->first(),
                'FREEDOM_OF_EXPRESSION' =>
                    DB::connection('cases')->table('case_referral_un_sp_freedom_expression')->where('referral_id', $referralId)->first(),
                'HUMAN_RIGHTS_DEFENDERS' =>
                    DB::connection('cases')->table('case_referral_un_sp_human_rights_defenders')->where('referral_id', $referralId)->first(),
                'EXTRAJUDICIAL_EXECUTIONS' =>
                    DB::connection('cases')->table('case_referral_un_sp_extrajudicial_executions')->where('referral_id', $referralId)->first(),
                'VIOLENCE_AGAINST_WOMEN' =>
                    DB::connection('cases')->table('case_referral_un_sp_violence_against_women')->where('referral_id', $referralId)->first(),
                'MINORITY_ISSUES' =>
                    DB::connection('cases')->table('case_referral_un_sp_minority_issues')->where('referral_id', $referralId)->first(),
                'FREEDOM_OF_RELIGION' =>
                    DB::connection('cases')->table('case_referral_un_sp_freedom_of_religion')->where('referral_id', $referralId)->first(),
                default => throw new \RuntimeException('Unsupported UN Special Procedure'),
            };

        } elseif (($case->referral_track ?? null) === 'HUMANITARIAN_PROTECTION') {

            if (($case->humanitarian_type ?? null) === 'ICRC') {

                $editorial = DB::connection('cases')
                    ->table('case_referral_humanitarian_icrc')
                    ->where('referral_id', $referralId)
                    ->first();

            } elseif (($case->humanitarian_type ?? null) === 'UNHCR') {

                $editorial = DB::connection('cases')
                    ->table('case_referral_humanitarian_unhcr')
                    ->where('referral_id', $referralId)
                    ->first();

            } else {
                throw new \RuntimeException('Unsupported humanitarian referral type.');
            }

        } elseif (($case->referral_track ?? null) === 'UN_ACCOUNTABILITY') {

            if (($case->entity_name ?? null) !== 'OHCHR') {
                throw new \RuntimeException('Unsupported UN Accountability entity.');
            }

            $editorial = DB::connection('cases')
                ->table('case_referral_un_accountability_ohchr')
                ->where('referral_id', $referralId)
                ->first();

        } elseif (($case->entity_name ?? null) === 'Amnesty International') {

            $editorial = DB::connection('cases')
                ->table('case_referral_ngo_amnesty')
                ->where('referral_id', $referralId)
                ->first();

        } else {

            // Legacy NGO
            $editorial = DB::connection('cases')
                ->table('case_referral_narratives')
                ->where('referral_id', $referralId)
                ->where('language', 'en')
                ->first();
        }

        if (!$editorial) {
            throw new \RuntimeException('Editorial content missing for this referral.');
        }

        if (!$isUN) {
            $summaryControls = DB::connection('cases')
                ->table('case_entity_referrals')
                ->where('id', $referralId)
                ->select([
                    'violation_classification',
                    'summary_alignment_note',
                ])
                ->first();

            if (!$summaryControls) {
                throw new \RuntimeException('Summary controls missing for this referral.');
            }
        }

        $natureTemplates = [
            'NGO_LEGAL' => [
                'ARBITRARY_ARREST_OR_DETENTION' =>
                    'Reported acts of arbitrary arrest or detention affecting personal liberty and security, as described by the source(s).',

                'ENFORCED_DISAPPEARANCE' =>
                    'Reported acts of enforced disappearance involving deprivation of liberty and concealment of the fate or whereabouts of the affected individual(s), as described by the source(s).',

                'TORTURE_OR_INHUMAN_TREATMENT' =>
                    'Reported acts of torture or other cruel, inhuman, or degrading treatment affecting physical and psychological integrity, as described by the source(s).',

                'THREATS_OR_INTIMIDATION' =>
                    'Reported acts of threats or intimidation affecting personal security and psychological well-being, as described by the source(s).',

                'DISCRIMINATION_BASED_VIOLATION' =>
                    'Reported acts involving discrimination based on identity or perceived affiliation, affecting dignity and equal enjoyment of fundamental rights, as described by the source(s).',

                'SEXUAL_OR_GENDER_BASED_VIOLENCE' =>
                    'Reported acts of sexual or gender-based violence affecting bodily integrity and personal security, as described by the source(s).',

                'PROPERTY_CONFISCATION_OR_DESTRUCTION' =>
                    'Reported acts involving confiscation or destruction of property affecting livelihood and economic security, as described by the source(s).',

                'LIVELIHOOD_RESTRICTION' =>
                    'Reported measures restricting livelihood or economic activity, affecting basic means of subsistence, as described by the source(s).',

                'FORCED_DISPLACEMENT' =>
                    'Reported acts leading to forced displacement affecting personal security, stability, and access to basic services, as described by the source(s).',

                'MULTIPLE_VIOLATIONS' =>
                    'A combination of reported acts affecting personal security, livelihood, and fundamental rights, as described by the source(s).',
            ],
        ];

        $natureOfIncident = '';

        if (!$isUN) {
            $track = $case->referral_track ?? 'NGO_LEGAL';

            $baseNatureText = $natureTemplates[$track][$summaryControls->violation_classification]
                ?? 'Reported acts affecting personal security and fundamental rights, as described by the source(s).';

            $natureOfIncident = trim(
                $baseNatureText . ' ' . ($summaryControls->summary_alignment_note ?? '')
            );
        }


        // 3) بناء المتغيرات للقالب
        $placeholders = array_merge($reportData, [

            'case_number' => $case->case_number,
            'legal_narrative' => $narrative,
            'report_date' => Carbon::now()->toDateString(),
            'submission_date' => optional($case->created_at)->toDateString(),
            'updates_count' => $updatesCount,
            'files_count' => $files->count(),
            'case_status_label' => $this->statusLabel($case->status, $lang),
            'verification_level_label' => 'Verified',
            'evidence_rows' => $this->buildEvidenceRows($files, $lang),
            'review_summary' => 'Reviewed internally according to established procedures.',
            'review_date' => Carbon::now()->toDateString(),
            'violation_summary' => $natureOfIncident,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Placeholders mapping
        |--------------------------------------------------------------------------
        */
        if (($case->referral_track ?? null) === 'SPECIAL_PROCEDURES') {
            
            /*
            |--------------------------------------------------------------------------
            | UN Special Procedures
            |--------------------------------------------------------------------------
            */
            if (($case->special_procedure_type ?? null) === 'TORTURE') {

                $placeholders['incident_summary_en']     = $editorial->incident_summary_en ?? '';
                $placeholders['victim_profile_en']        = $editorial->victim_profile_en ?? '';
                $placeholders['alleged_perpetrators_en']  = $editorial->alleged_perpetrators_en ?? '';
                $placeholders['context_pattern_en']       = $editorial->context_pattern_en ?? '';
                $placeholders['remedies_exhausted_en']    = $editorial->remedies_exhausted_en ?? '';

            } elseif (($case->special_procedure_type ?? null) === 'ENFORCED_DISAPPEARANCE') {

                $placeholders['incident_summary_en']     = $editorial->incident_summary_en ?? '';
                $placeholders['victim_information_en']   = $editorial->victim_information_en ?? '';
                $placeholders['circumstances_en']        = $editorial->circumstances_en ?? '';
                $placeholders['alleged_perpetrators_en'] = $editorial->alleged_perpetrators_en ?? '';
                $placeholders['context_pattern_en']      = $editorial->context_pattern_en ?? '';
                $placeholders['steps_taken_en']           = $editorial->steps_taken_en ?? '';
            
            } elseif (($case->special_procedure_type ?? null) === 'ARBITRARY_DETENTION') {

                $placeholders['incident_summary_en']      = $editorial->incident_summary_en ?? '';
                $placeholders['victim_information_en']    = $editorial->victim_information_en ?? '';
                $placeholders['detention_details_en']     = $editorial->detention_details_en ?? '';
                $placeholders['legal_basis_en']            = $editorial->legal_basis_en ?? '';
                $placeholders['procedural_violations_en'] = $editorial->procedural_violations_en ?? '';
                $placeholders['context_pattern_en']        = $editorial->context_pattern_en ?? '';
                $placeholders['remedies_exhausted_en']     = $editorial->remedies_exhausted_en ?? '';

            } elseif (($case->special_procedure_type ?? null) === 'FREEDOM_OF_EXPRESSION') {

                $placeholders['incident_summary_en']      = $editorial->incident_summary_en ?? '';
                $placeholders['victim_information_en']    = $editorial->victim_information_en ?? '';
                $placeholders['expression_activity_en']   = $editorial->expression_activity_en ?? '';
                $placeholders['violations_details_en']    = $editorial->violations_details_en ?? '';
                $placeholders['legal_basis_en']            = $editorial->legal_basis_en ?? '';
                $placeholders['context_pattern_en']        = $editorial->context_pattern_en ?? '';
                $placeholders['remedies_exhausted_en']     = $editorial->remedies_exhausted_en ?? '';

            } elseif (($case->special_procedure_type ?? null) === 'HUMAN_RIGHTS_DEFENDERS') {

                $placeholders['incident_summary_en']      = $editorial->incident_summary_en ?? '';
                $placeholders['victim_information_en']    = $editorial->victim_information_en ?? '';
                $placeholders['defender_role_en']          = $editorial->defender_role_en ?? '';
                $placeholders['activities_description_en'] = $editorial->activities_description_en ?? '';
                $placeholders['targeting_link_en']         = $editorial->targeting_link_en ?? '';
                $placeholders['violations_details_en']     = $editorial->violations_details_en ?? '';
                $placeholders['context_pattern_en']        = $editorial->context_pattern_en ?? '';
                $placeholders['remedies_exhausted_en']     = $editorial->remedies_exhausted_en ?? '';
            
            } elseif (($case->special_procedure_type ?? null) === 'EXTRAJUDICIAL_EXECUTIONS') {

                $placeholders['incident_summary_en']            = $editorial->incident_summary_en ?? '';
                $placeholders['victim_information_en']          = $editorial->victim_information_en ?? '';
                $placeholders['circumstances_of_killing_en']    = $editorial->circumstances_of_killing_en ?? '';
                $placeholders['alleged_perpetrators_en']        = $editorial->alleged_perpetrators_en ?? '';
                $placeholders['context_pattern_en']             = $editorial->context_pattern_en ?? '';
                $placeholders['remedies_exhausted_en']          = $editorial->remedies_exhausted_en ?? '';

            } elseif (($case->special_procedure_type ?? null) === 'VIOLENCE_AGAINST_WOMEN') {

                $placeholders['incident_summary_en']     = $editorial->incident_summary_en ?? '';
                $placeholders['victim_information_en']   = $editorial->victim_information_en ?? '';
                $placeholders['violence_description_en']     = $editorial->violence_description_en ?? '';
                $placeholders['alleged_perpetrators_en'] = $editorial->alleged_perpetrators_en ?? '';
                $placeholders['context_pattern_en']      = $editorial->context_pattern_en ?? '';
                $placeholders['remedies_exhausted_en']   = $editorial->remedies_exhausted_en ?? '';

            } elseif (($case->special_procedure_type ?? null) === 'MINORITY_ISSUES') {

                $placeholders['incident_summary_en']               = $editorial->incident_summary_en ?? '';
                $placeholders['victim_information_en']             = $editorial->victim_information_en ?? '';
                $placeholders['minority_or_religious_identity_en'] = $editorial->minority_or_religious_identity_en ?? '';
                $placeholders['violation_description_en']          = $editorial->violation_description_en ?? '';
                $placeholders['alleged_perpetrators_en']           = $editorial->alleged_perpetrators_en ?? '';
                $placeholders['context_pattern_en']                = $editorial->context_pattern_en ?? '';
                $placeholders['remedies_exhausted_en']             = $editorial->remedies_exhausted_en ?? '';

            } elseif (($case->special_procedure_type ?? null) === 'FREEDOM_OF_RELIGION') {

                $placeholders['incident_summary_en']               = $editorial->incident_summary_en ?? '';
                $placeholders['victim_information_en']             = $editorial->victim_information_en ?? '';
                $placeholders['minority_or_religious_identity_en'] = $editorial->minority_or_religious_identity_en ?? '';
                $placeholders['violation_description_en']          = $editorial->violation_description_en ?? '';
                $placeholders['alleged_perpetrators_en']           = $editorial->alleged_perpetrators_en ?? '';
                $placeholders['context_pattern_en']                = $editorial->context_pattern_en ?? '';
                $placeholders['remedies_exhausted_en']             = $editorial->remedies_exhausted_en ?? '';

            }

        } elseif (($case->referral_track ?? null) === 'HUMANITARIAN_PROTECTION') {

            /*
            |--------------------------------------------------------------------------
            | Humanitarian Protection – ICRC / UNHCR
            |--------------------------------------------------------------------------
            */

            $placeholders['source_account_en']       = $editorial->source_account_en ?? '';
            $placeholders['general_location']        = $editorial->general_location_en ?? '';
            $placeholders['incident_timeframe']      = $editorial->incident_timeframe_en ?? '';
            $placeholders['humanitarian_needs_en']   = $editorial->humanitarian_needs_en ?? '';
            $placeholders['immediate_risks_en']      = $editorial->immediate_risks_en ?? '';
            $placeholders['mandate_relevance_en']    = $editorial->mandate_relevance_en ?? '';
            $placeholders['assistance_requested_en'] = $editorial->assistance_requested_en ?? '';

        } elseif (($case->referral_track ?? null) === 'UN_ACCOUNTABILITY') {

            $placeholders['source_context_en']            = $editorial->source_context_en ?? '';
            $placeholders['methodology_note_en']          = $editorial->methodology_note_en ?? '';
            $placeholders['general_location']             = $editorial->general_location_en ?? '';
            $placeholders['incident_timeframe']           = $editorial->incident_timeframe_en ?? '';
            $placeholders['documented_information_en']    = $editorial->documented_information_en ?? '';
            $placeholders['identified_concerns_en']       = $editorial->identified_concerns_en ?? '';
            $placeholders['pattern_observation_en']       = $editorial->pattern_observation_en ?? '';
            $placeholders['mandate_relevance_en']          = $editorial->mandate_relevance_en ?? '';
            $placeholders['additional_notes_internal']    = $editorial->additional_notes_internal ?? '';
        
        } else {

            /*
            |--------------------------------------------------------------------------
            | NGO / Amnesty / Legacy
            |--------------------------------------------------------------------------
            */

            if (($case->entity_name ?? null) === 'Amnesty International') {

                $placeholders['source_account_en']        = $editorial->source_account_en ?? '';
                $placeholders['general_location']         = $editorial->general_location_en ?? '';
                $placeholders['incident_timeframe']       = $editorial->incident_timeframe_en ?? '';
                $placeholders['violation_summary']        = $editorial->violation_summary_en ?? '';
                $placeholders['psychosocial_impact_text'] = $editorial->psychosocial_impact_en ?? '';

            } else {

                // Legacy NGO
                $placeholders['general_location']         = $editorial->general_location_en ?? '';
                $placeholders['incident_timeframe']       = $editorial->incident_timeframe_en ?? '';
                $placeholders['psychosocial_impact_text'] = $editorial->psychosocial_impact_en ?? '';
            }
        }
        if (
            ($case->referral_track ?? null) === 'NGO_LEGAL' &&
            ($case->entity_name ?? null) !== 'Amnesty International' &&
            !$narrative
        ) {
            throw new \RuntimeException('Legal narrative is missing for this referral.');
        }

        // 4) اختيار القالب
        $viewName = match ($case->referral_track ?? null) {

            'HUMANITARIAN_PROTECTION' => match ($case->humanitarian_type) {
                'ICRC'  => 'reports.templates.humanitarian.icrc.icrc-humanitarian',
                'UNHCR' => 'reports.templates.humanitarian.unhcr.unhcr-humanitarian',
                default => throw new \RuntimeException('Unsupported humanitarian template'),
            },

            'UN_ACCOUNTABILITY' =>
                'reports.templates.un.accountability.ohchr-accountability',

            'NGO_LEGAL' => match ($case->entity_name ?? null) {
                'Amnesty International' =>
                    'reports.templates.ngo.amnesty',
                default =>
                    'reports.templates.ngo.legal',
            },

            'SPECIAL_PROCEDURES' => match ($case->special_procedure_type) {
                'TORTURE' =>
                    'reports.templates.un.special-procedures.torture',

                'ENFORCED_DISAPPEARANCE' =>
                    'reports.templates.un.special-procedures.enforced-disappearance',

                'ARBITRARY_DETENTION' =>
                    'reports.templates.un.special-procedures.arbitrary-detention',

                'FREEDOM_OF_EXPRESSION' =>
                    'reports.templates.un.special-procedures.freedom-expression',

                'HUMAN_RIGHTS_DEFENDERS' =>
                    'reports.templates.un.special-procedures.human-rights-defenders',

                'EXTRAJUDICIAL_EXECUTIONS' =>
                    'reports.templates.un.special-procedures.extrajudicial-executions',

                'VIOLENCE_AGAINST_WOMEN' =>
                    'reports.templates.un.special-procedures.violence-against-women',

                'MINORITY_ISSUES' =>
                    'reports.templates.un.special-procedures.minority-issues',

                'FREEDOM_OF_RELIGION' =>
                    'reports.templates.un.special-procedures.freedom-of-religion',

                default =>
                    throw new \RuntimeException('Unsupported UN Special Procedure'),
            },            
        };


        $html = View::make($viewName, $placeholders)->render();
        $htmlContent = $html;

        // 9) النتيجة
        return [
            'html' => $html,
            'language' => $lang,
            'identity_masked' => $identityMasked,
        ];
    }

    // -------- Helpers --------

    protected function anonymize(CaseModel $case, bool $mask): array
    {
        if ($mask) {
            return [
                'general_location' => $this->generalizeLocation($case->location),
                'incident_timeframe' => $this->generalizeDate($case->threat_date),
                'violation_summary' => $case->direct_threat ? 'Reported threats' : 'Reported incident',
                'sanitized_case_description' => $case->threat_description ?? '',
                'psychosocial_impact_text' => $case->psychological_impact
                    ? ($case->impact_details ?? 'Reported psychological impact.')
                    : 'No reported psychological impact.',
            ];
        }

        // في حال قررت الإدارة إظهار الهوية (استثناء)
        return [
            'general_location' => $case->location,
            'incident_timeframe' => optional($case->threat_date)->toDateString(),
            'violation_summary' => $case->direct_threat ? 'Direct threat reported' : 'Incident reported',
            'sanitized_case_description' => $case->threat_description ?? '',
            'psychosocial_impact_text' => $case->impact_details ?? '',
        ];
    }

    protected function buildEvidenceRows($files, string $lang): string
    {
        $rows = '';
        $i = 1;

        foreach ($files as $f) {
            $description = match (true) {
                str_starts_with($f->mime_type ?? '', 'image/') =>
                    'Image file submitted by the reporting source',
                str_starts_with($f->mime_type ?? '', 'video/') =>
                    'Video file submitted by the reporting source',
                str_starts_with($f->mime_type ?? '', 'audio/') =>
                    'Audio recording submitted by the reporting source',
                $f->mime_type === 'application/pdf' =>
                    'Document (PDF) submitted by the reporting source',
                default =>
                    'Supporting file submitted by the reporting source',
            };

            $date = $f->created_at
                ? \Carbon\Carbon::parse($f->created_at)->toDateString()
                : '—';

            $rows .= '<tr>'
                . '<td>' . $i++ . '</td>'
                . '<td>' . e($this->mapMimeType($f->mime_type, $lang)) . '</td>'
                . '<td>' . e($description) . '</td>'
                . '<td>' . e($date) . '</td>'
                . '</tr>';
        }

        return $rows;
    }

    protected function mapMimeType(?string $mime, string $lang): string
    {
        if (!$mime) {
            return $lang === 'AR' ? 'ملف' : 'File';
        }

        if (str_starts_with($mime, 'image/')) {
            return $lang === 'AR' ? 'صورة' : 'Image';
        }

        if ($mime === 'application/pdf') {
            return 'PDF';
        }

        if (str_starts_with($mime, 'video/')) {
            return $lang === 'AR' ? 'فيديو' : 'Video';
        }

        return $lang === 'AR' ? 'ملف' : 'File';
    }

    protected function statusLabel(string $status, string $lang): string
    {
        $map = [
            'new' => ['AR' => 'جديدة', 'EN' => 'New'],
            'under_review' => ['AR' => 'قيد المراجعة', 'EN' => 'Under Review'],
            'verified' => ['AR' => 'تم التحقق', 'EN' => 'Verified'],
            'ready_for_export' => ['AR' => 'جاهزة للتصدير', 'EN' => 'Ready for Export'],
            'exported' => ['AR' => 'تم التصدير', 'EN' => 'Exported'],
            'archived' => ['AR' => 'مؤرشفة', 'EN' => 'Archived'],
            'rejected' => ['AR' => 'مرفوضة', 'EN' => 'Rejected'],
        ];
        return $map[$status][$lang] ?? $status;
    }

    protected function generalizeLocation(?string $location): string
    {
        if (!$location) return '';
        // تبسيط: خذ الجزء العام فقط
        return explode(',', $location)[0];
    }

    protected function generalizeDate($date): string
    {
        if (!$date) return '';
        return Carbon::parse($date)->format('Y');
    }
}
