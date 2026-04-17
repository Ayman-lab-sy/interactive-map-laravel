<div class="report-en">

<style>
    .report-en {
        font-family: "Times New Roman", Georgia, serif;
        direction: ltr;
        text-align: left;
        line-height: 1.9;
        font-size: 14px;
        color: #000;
        margin: 30px 35px;
    }

    .report-en .cover {
        text-align: center;
        margin-top: 80px;
    }

    .report-en .cover h1 {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .report-en .cover h2 {
        font-size: 16px;
        font-weight: normal;
        margin-bottom: 22px;
    }

    .report-en .section-title {
        font-size: 18px;
        font-weight: bold;
        margin-top: 24px;
        margin-bottom: 12px;
        border-bottom: 2px solid #000;
        padding-bottom: 6px;
        page-break-after: avoid;
        page-break-inside: avoid;
    }

    .report-en table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .report-en table,
    .report-en p {
        page-break-inside: avoid;
    }

    .report-en table td {
        border: 1px solid #000;
        padding: 8px;
        vertical-align: top;
        width: 35%;
        font-weight: bold;
    }

    .report-en table td.value {
        width: 65%;
        font-weight: normal;
    }

    .report-en .page-break {
        page-break-after: always;
    }

    .report-en .note {
        font-size: 13px;
        font-style: italic;
        margin-top: 15px;
    }

    .report-en p {
        margin: 8px 0;
        orphans: 2;
        widows: 2;
    }

    .org-logo {
        width: 90px;
        margin-bottom: 15px;
    }
</style>

{{-- ========================= --}}
{{-- Cover Page --}}
{{-- ========================= --}}
<div class="cover">

    <img src="logo.png"
         alt="Organization Logo"
         class="org-logo">

    <h1>Humanitarian Protection & Displacement Brief</h1>

    <h2>Confidential Humanitarian Submission</h2>

    <p><strong>Submitted to:</strong> United Nations High Commissioner for Refugees (UNHCR)</p>
    <p><strong>Submitted by:</strong> Syrian Alawites and Minorities Organization</p>
    <p><strong>Date:</strong> {{ $report_date }}</p>
    <p><strong>Reference:</strong> {{ $case_number }}</p>

    <p class="note">
        This document is a confidential humanitarian submission intended to support
        protection analysis, humanitarian planning, and assistance activities
        within the mandate of UNHCR.
        <br><br>
        It does not constitute a legal assessment, public allegation, or attribution
        of responsibility.
    </p>

</div>

<div class="page-break"></div>

{{-- ========================= --}}
{{-- 1. Reporting Organization --}}
{{-- ========================= --}}
<div class="section-title">1. Reporting Organization</div>

<p>
The Syrian Alawites and Minorities Organization is a Europe-based civil and
humanitarian organization operating from Austria. The Organization receives,
documents, and reviews humanitarian-related information submitted by community
sources and local contacts from affected areas.
</p>

<p>
Due to security considerations, the Organization does not conduct direct field
operations inside Syria. This submission is prepared to support humanitarian
protection and displacement-related assessment activities, in accordance with
the principles of neutrality, impartiality, and humanity.
</p>

{{-- ========================= --}}
{{-- 2. Source of Information --}}
{{-- ========================= --}}
<div class="section-title">2. Source of Information</div>

<p>
The information contained in this brief is based on reports and communications
received by the Organization from community-based sources and local contacts
within affected areas.
</p>

<p>
The Organization has not conducted independent field verification of the reported
information. This submission is provided in good faith for humanitarian protection
and assessment purposes only.
</p>

{{-- ========================= --}}
{{-- 3. Source Account --}}
{{-- ========================= --}}
<div class="section-title">3. Source Account (As Received)</div>

<p>
{!! nl2br(e($source_account_en)) !!}
</p>

<p>
The information was reported by a community-based civilian source with direct
knowledge of the reported situation.
</p>

<p class="note">
This section reflects the information as received by the Organization from the
reporting source. It has not been independently verified and is included for
humanitarian understanding purposes only.
</p>

{{-- ========================= --}}
{{-- 4. Location and Timeframe --}}
{{-- ========================= --}}
<div class="section-title">4. Location and Timeframe</div>

<table>
    <tr>
        <td><strong>Location</strong></td>
        <td class="value">
            {{ $general_location }}
            <br>
            <span style="font-size:13px; font-style:italic;">
                (General geographic reference)
            </span>
        </td>
    </tr>
    <tr>
        <td><strong>Timeframe</strong></td>
        <td class="value">
            {{ $incident_timeframe }}
        </td>
    </tr>
</table>

{{-- ========================= --}}
{{-- 5. Situation Overview --}}
{{-- ========================= --}}
<div class="section-title">5. Situation Overview</div>

<p>
Based on the information received, affected individuals and communities in the
above-mentioned area are experiencing humanitarian and protection-related
challenges, including displacement risks, reduced access to essential services,
and heightened vulnerability.
</p>

<p>
The reported situation raises protection concerns relevant to UNHCR’s mandate,
particularly with regard to civilians, displaced persons, and other vulnerable
groups.
</p>

{{-- ========================= --}}
{{-- 6. Identified Humanitarian Needs --}}
{{-- ========================= --}}
<div class="section-title">6. Identified Humanitarian Needs</div>

<p>
Based on the information available at the time of reporting, the following
humanitarian and protection-related needs have been identified:
</p>

<ul>
@foreach(preg_split("/\r\n|\n|\r/", $humanitarian_needs_en) as $need)
    @if(trim($need))
        <li>{{ $need }}</li>
    @endif
@endforeach
</ul>

{{-- ========================= --}}
{{-- 7. Immediate Risks --}}
{{-- ========================= --}}
<div class="section-title">7. Immediate Risks</div>

<p>
If the current situation persists without adequate humanitarian and protection
support, affected individuals may face the following immediate risks:
</p>

<p>{{ $immediate_risks_en }}</p>

{{-- ========================= --}}
{{-- 8. Relevance to UNHCR Mandate --}}
{{-- ========================= --}}
<div class="section-title">8. Relevance to the UNHCR Protection Mandate</div>

<p>
The situation described above is of humanitarian concern due to its direct impact
on displaced persons, persons at risk of displacement, and other vulnerable
civilian populations.
</p>

<p>
Such circumstances fall within the protection and assistance mandate of UNHCR,
including protection monitoring, displacement response, and support to affected
communities in coordination with relevant humanitarian partners.
</p>

{{-- ========================= --}}
{{-- 9. Protection / Case Snapshot (Optional) --}}
{{-- ========================= --}}
<div class="section-title">9. Protection / Case Snapshot </div>

@if(!empty($case_snapshot_en))
    <p>
        {{ $case_snapshot_en }}
    </p>
@else
    <p style="font-style: italic; color: #555;">
        No specific protection snapshot or urgent protection concerns were reported
        at the time of submission.
    </p>
@endif


{{-- ========================= --}}
{{-- 10. Requested Humanitarian Assistance --}}
{{-- ========================= --}}
<div class="section-title">10. Requested Humanitarian Assistance</div>

<p>
In light of the above, we respectfully request UNHCR to consider the reported
situation for protection monitoring, humanitarian assessment, and appropriate
assistance measures in line with its mandate, and in coordination with relevant
humanitarian actors where appropriate.
</p>

{{-- ========================= --}}
{{-- 11. Closing --}}
{{-- ========================= --}}
<div class="section-title">11. Closing</div>

<p>
This submission is made in good faith to support humanitarian protection and
assistance efforts and to contribute to the safety, dignity, and well-being of
affected populations.
</p>

<p>
The Organization remains available to provide any additional information, should
it be required.
</p>

<p><strong>Sincerely,</strong><br>
Syrian Alawites and Minorities Organization
</p>

</div>
