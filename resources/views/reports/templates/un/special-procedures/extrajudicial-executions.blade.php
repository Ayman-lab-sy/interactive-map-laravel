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

    .section {
        margin-bottom: 26px;
        page-break-inside: auto;
    }

    .section-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 10px;
        border-bottom: 1px solid #000;
        padding-bottom: 4px;
        page-break-after: avoid;
    }

    p {
        margin: 8px 0;
        orphans: 2;
        widows: 2;
    }

    .note {
        font-size: 13px;
        font-style: italic;
        margin-top: 10px;
    }

    .page-break {
        page-break-after: always;
    }
</style>

{{-- Header --}}
<div class="section">
    <p><strong>To:</strong> United Nations Special Procedures</p>
    <p>
        <strong>Mandate:</strong>
        Special Rapporteur on extrajudicial, summary or arbitrary executions
    </p>
    <p>
        <strong>Subject:</strong>
        Alleged extrajudicial, summary or arbitrary execution
    </p>
    <p><strong>Case Reference:</strong> {{ $case_number }}</p>
    <p><strong>Date of Submission:</strong> {{ $report_date }}</p>
</div>

<div class="section">
    <p>
        The present submission is respectfully submitted to the
        United Nations Special Rapporteur on extrajudicial, summary or
        arbitrary executions, in accordance with the mandate to examine
        situations involving allegations of unlawful deprivation of life.
    </p>

    <p>
        The information contained herein has been received and documented
        by a civil society source and is submitted in good faith for the
        purposes of international human rights monitoring. Identifying
        details have been handled with due regard to safety and protection
        considerations.
    </p>

    <p>
        This submission is presented in accordance with the working methods
        of the United Nations Special Rapporteur on extrajudicial, summary
        or arbitrary executions. The information contained herein is
        submitted strictly as reported, without legal qualification,
        determination, or attribution of responsibility.
    </p>
</div>

{{-- I. Summary of Alleged Facts --}}
<div class="section">
    <div class="section-title">I. Summary of Alleged Facts</div>

    <p>
        The following information concerns allegations relating to the death
        of an individual, reportedly occurring under circumstances that may
        raise concerns under the mandate of the Special Rapporteur on
        extrajudicial, summary or arbitrary executions. The facts summarized
        below are presented strictly as alleged, without legal qualification
        or attribution of responsibility.
    </p>

    <p>
        {{ $incident_summary_en }}
    </p>

    <p class="note">
        This summary reflects the information as reported. Its inclusion does
        not imply verification or legal assessment by the submitting
        organization.
    </p>
</div>

{{-- II. Information on the Individual(s) --}}
<div class="section">
    <div class="section-title">II. Information on the Individual(s)</div>

    <p>
        The individual(s) concerned are described below, based on the
        information provided by the source. Certain identifying details may
        be included where relevant, while others have been withheld for
        protection purposes.
    </p>

    <p>
        {{ $victim_information_en }}
    </p>
</div>

{{-- III. Circumstances of the Killing / Death --}}
<div class="section">
    <div class="section-title">III. Circumstances of the Killing / Death</div>

    <p>
        This section summarizes the circumstances surrounding the reported
        killing or death, including the time, location, manner, and context
        in which the incident allegedly occurred, as described by the
        source.
    </p>

    <p>
        {{ $circumstances_of_killing_en }}
    </p>
</div>

{{-- IV. Alleged Perpetrators --}}
<div class="section">
    <div class="section-title">IV. Alleged Perpetrators</div>

    <p>
        The following information summarizes details regarding the
        individual(s), group(s), or authority(ies) reportedly involved in
        the incident, as described by the source. This information is
        presented without attribution of legal responsibility.
    </p>

    <p>
        {{ $alleged_perpetrators_en }}
    </p>
</div>

{{-- V. Context / Pattern --}}
<div class="section">
    <div class="section-title">V. Context / Pattern</div>

    <p>
        The following information is provided for contextual purposes only
        and does not seek to establish the existence of a systematic policy
        or practice.
    </p>

    @if(!empty($context_pattern_en))
        <p>{{ $context_pattern_en }}</p>
    @else
        <p>
            Based on the information available to the source, the reported
            incident does not appear to form part of a broader or systematic
            pattern and is presented as an isolated case.
        </p>
    @endif
</div>

{{-- VI. Steps Taken / Remedies --}}
<div class="section">
    <div class="section-title">VI. Steps Taken / Remedies</div>

    <p>
        Information regarding steps reportedly taken at the domestic level
        in relation to the incident, or the reasons why such remedies were
        unavailable, ineffective, or would have posed a risk, is summarized
        below according to the information received.
    </p>

    <p>
        {{ $remedies_exhausted_en }}
    </p>

    <p class="note">
        This information is provided in accordance with the working methods
        of the United Nations Special Rapporteur on extrajudicial, summary or
        arbitrary executions.
    </p>
</div>

<div class="section">
    <p>
        This submission is provided in good faith for the purposes of
        international human rights monitoring and falls within the mandate
        of the United Nations Special Rapporteur on extrajudicial, summary
        or arbitrary executions.
    </p>

    <p>
        Identifying details of the individual(s) concerned and the source
        have been withheld or anonymized where necessary for protection
        purposes.
    </p>

    <p class="note">
        Supporting documentation may be provided upon request, subject to
        consent and protection considerations.
    </p>
</div>

</div>
