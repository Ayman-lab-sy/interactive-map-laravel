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
        margin-top: 28px;
        margin-bottom: 12px;
        border-bottom: 2px solid #000;
        padding-bottom: 6px;
        page-break-after: avoid;
        page-break-inside: avoid;
    }

    .report-en p {
        margin: 8px 0;
        orphans: 2;
        widows: 2;
        page-break-inside: avoid;
    }

    .report-en table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
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

    .report-en .note {
        font-size: 13px;
        font-style: italic;
        margin-top: 15px;
        color: #333;
    }

    .page-break {
        page-break-after: always;
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

    <img src="logo.png" alt="Organization Logo" class="org-logo">

    <h1>Confidential Human Rights Documentation Submission</h1>
    <h2>Confidential Institutional Submission</h2>

    <p><strong>Submitted to:</strong> Office of the United Nations High Commissioner for Human Rights (OHCHR)</p>
    <p><strong>Submitted by:</strong> Syrian Alawites and Minorities Organization</p>
    <p><strong>Date:</strong> {{ $report_date }}</p>
    <p><strong>Reference:</strong> {{ $case_number }}</p>

    <p class="note">
        This document is a confidential institutional submission intended solely for
        information, documentation, and accountability-related purposes within the
        mandate of the Office of the United Nations High Commissioner for Human Rights.
        <br><br>
        It does not constitute a legal determination, judicial finding, or attribution
        of criminal responsibility.
    </p>

</div>

<div class="page-break"></div>

{{-- ========================= --}}
{{-- 1. Reporting Organization --}}
{{-- ========================= --}}
<div class="section-title">1. Reporting Organization</div>

<p>
The Syrian Alawites and Minorities Organization is an independent, Europe-based
civil and human rights organization operating from Austria. The Organization receives, documents,
and reviews information related to human rights concerns and civilian protection
from community-based sources and local contacts.
</p>

<p>
Due to security considerations, the Organization does not conduct direct field
operations inside Syria. This submission is prepared for documentation and
institutional accountability purposes, in accordance with principles of neutrality,
objectivity, and responsible reporting.
</p>

{{-- ========================= --}}
{{-- 2. Methodology --}}
{{-- ========================= --}}
<div class="section-title">2. Methodology</div>

<p>
The Organization collects information through direct communications with
community-based sources and local contacts. All submissions undergo an internal
review process aimed at assessing consistency, credibility, and relevance to the
Organization’s documentation mandate.
</p>

<p>
The Organization does not conduct on-site verification and presents the information
as received, for institutional documentation and accountability-related purposes
only.
</p>

@if(!empty($methodology_note_en))
<p>
{!! nl2br(e($methodology_note_en)) !!}
</p>
@endif

<p style="font-style: italic;">
This section outlines the internal methodology applied by the Organization in
collecting, reviewing, and organizing the reported information.
</p>

{{-- ========================= --}}
{{-- 3. Contextual Overview of the Situation --}}
{{-- ========================= --}}
<div class="section-title">3. Contextual Overview of the Situation</div>

<p>
The following overview reflects contextual information as communicated to the
Organization by community-based sources and local contacts.
</p>

<p>
{!! nl2br(e($source_context_en)) !!}
</p>

<p style="font-style: italic;">
This overview provides a general contextual description of the situation as
received by the Organization, without attribution of responsibility or legal
characterization.
</p>

{{-- ========================= --}}
{{-- 4. Location and Timeframe --}}
{{-- ========================= --}}
<div class="section-title">4. Location and Timeframe</div>

<table>
    <tr>
        <td>Location</td>
        <td class="value">
            {{ $general_location }}
            <br>
            <span style="font-size:13px; font-style:italic;">
                (General geographic reference, as reported to the Organization)
            </span>
        </td>
    </tr>
    <tr>
        <td>Timeframe</td>
        <td class="value">
            {{ $incident_timeframe }}
            <br>
            <span style="font-size:13px; font-style:italic;">
                (Approximate timeframe based on source reporting)
            </span>
        </td>
    </tr>
</table>

{{-- ========================= --}}
{{-- 5. Factual Information (As Documented) --}}
{{-- ========================= --}}
<div class="section-title">5. Factual Information (As Documented)</div>

<p style="font-size:13px; font-style:italic; margin-bottom:10px;">
The following information reflects factual details as documented and compiled by
the Organization based on source reporting. It is presented without legal
qualification, attribution of responsibility, or analytical interpretation.
</p>

<p>
{!! nl2br(e($documented_information_en)) !!}
</p>

{{-- ========================= --}}
{{-- 6. Identified Human Rights Concerns --}}
{{-- ========================= --}}
<div class="section-title">6. Identified Human Rights Concerns</div>

<p style="font-size:13px; font-style:italic; margin-bottom:10px;">
The following concerns have been identified by the Organization based on the
documented information received. These concerns are presented for institutional
awareness and mandate relevance purposes only and do not constitute legal findings
or determinations of responsibility.
</p>

<ul>
@foreach(preg_split("/\r\n|\n|\r/", $identified_concerns_en) as $concern)
    @if(trim($concern))
        <li>{{ $concern }}</li>
    @endif
@endforeach
</ul>

{{-- ========================= --}}
{{-- 7. Observed Patterns --}}
{{-- ========================= --}}
<div class="section-title">7. Observed Patterns</div>

@if(!empty($pattern_observation_en))
    <p style="font-size:13px; font-style:italic; margin-bottom:10px;">
        The following observations reflect potential patterns or recurring elements
        identified by the Organization based on the information received. These
        observations are analytical in nature and do not constitute legal findings
        or determinations of responsibility.
    </p>

    <p>
        {!! nl2br(e($pattern_observation_en)) !!}
    </p>
@else
    <p style="font-style: italic; color: #555;">
        Based on the information available at the time of documentation, the
        Organization did not identify sufficient elements to indicate specific
        patterns or recurring practices. This does not preclude the seriousness
        of the documented information.
    </p>
@endif


{{-- ========================= --}}
{{-- 8. Relevance to the OHCHR Mandate --}}
{{-- ========================= --}}
<div class="section-title">8. Relevance to the OHCHR Mandate</div>

<p>
{!! nl2br(e($mandate_relevance_en)) !!}
</p>

<p class="note">
The relevance outlined above reflects an institutional assessment by the
Organization regarding the potential applicability of the OHCHR mandate,
including monitoring, reporting, and preventive engagement functions.
<br><br>
This assessment is provided for informational and institutional purposes only
and does not constitute a legal qualification, finding, or determination of
responsibility under international law.
</p>

{{-- ========================= --}}
{{-- 9. Closing --}}
{{-- ========================= --}}
<div class="section-title">9. Closing</div>

<p>
This submission is provided in good faith to support the documentation,
monitoring, and institutional accountability functions of the Office of the
United Nations High Commissioner for Human Rights.
</p>

<p>
The Organization remains available to provide additional clarification or
 supplementary information upon request, subject to confidentiality and protection considerations.
</p>

<p><strong>Sincerely,</strong><br>
Syrian Alawites and Minorities Organization
</p>

</div>
