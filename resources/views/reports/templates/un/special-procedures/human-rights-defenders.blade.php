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
        Special Rapporteur on the situation of human rights defenders
    </p>
    <p>
        <strong>Subject:</strong>
        Alleged acts of targeting or interference affecting a human rights defender
    </p>
    <p><strong>Case Reference:</strong> {{ $case_number }}</p>
    <p><strong>Date of Submission:</strong> {{ $report_date }}</p>
</div>

<div class="section">
    <p>
        The present submission is respectfully submitted to the
        United Nations Special Rapporteur on the situation of human rights
        defenders, in accordance with the mandate to identify, examine, and
        advise on situations involving alleged acts of targeting, reprisals,
        or interference against individuals engaged in human rights-related
        activities.
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
        of the United Nations Special Rapporteur on the situation of human
        rights defenders. The information contained herein is submitted
        strictly as reported, without legal qualification or attribution of
        responsibility.
    </p>
</div>

{{-- I. Summary of Alleged Facts --}}
<div class="section">
    <div class="section-title">I. Summary of Alleged Facts</div>

    <p>
        The following information concerns allegations of acts or measures
        reportedly directed against a human rights defender, in connection
        with their human rights-related role or activities. The facts
        summarized below are presented strictly as alleged, without legal
        qualification or attribution of responsibility. The information is presented in a factual manner and, where possible,
        in chronological order, based solely on the account received.
    </p>

    <p>
        {{ $incident_summary_en }}
    </p>

    <p class="note">
        This summary reflects the information as reported. Its inclusion does not
        imply verification or legal assessment by the submitting organization.
    </p>
</div>

{{-- II. Information on the Human Rights Defender --}}
<div class="section">
    <div class="section-title">II. Information on the Human Rights Defender</div>

    <p>
        The individual concerned is described below, based on the information
        provided by the source. Certain identifying details may be included
        where relevant, while others have been withheld for protection
        purposes.
    </p>

    <p>
        {{ $victim_information_en }}
    </p>
</div>

{{-- III. Role as a Human Rights Defender --}}
<div class="section">
    <div class="section-title">III. Role as a Human Rights Defender</div>

    <p>
        This section outlines the role reportedly undertaken by the
        individual as a human rights defender, including their area of
        engagement, affiliation, or function, as described by the source.
    </p>

    <p>
        {{ $defender_role_en }}
    </p>
</div>

{{-- IV. Description of Activities --}}
<div class="section">
    <div class="section-title">IV. Description of Activities</div>

    <p>
        The following information describes the nature of the human rights-
        related activities reportedly carried out by the individual,
        including advocacy, documentation, monitoring, legal assistance,
        awareness-raising, or other relevant actions, as reported.
    </p>

    <p>
        {{ $activities_description_en }}
    </p>
</div>

{{-- V. Link Between Activities and Targeting --}}
<div class="section">
    <div class="section-title">V. Link Between Activities and Targeting</div>

    <p>
        This section summarizes the reported link between the individual’s
        human rights-related activities and the acts or measures allegedly
        directed against them, as described by the source.
    </p>

    <p>
        {{ $targeting_link_en }}
    </p>
</div>

{{-- VI. Alleged Violations or Reprisals --}}
<div class="section">
    <div class="section-title">VI. Alleged Violations or Reprisals</div>

    <p>
        The following information summarizes the acts or measures reportedly
        experienced by the human rights defender, including threats,
        harassment, surveillance, arrest, detention, intimidation,
        defamation, or other forms of pressure, as reported.
    </p>

    <p>
        {{ $violations_details_en }}
    </p>
</div>

{{-- VII. Context / Pattern --}}
<div class="section">
    <div class="section-title">VII. Context / Pattern</div>

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
            acts do not appear to form part of a broader or systematic
            pattern and are presented as an isolated case.
        </p>
    @endif
</div>

{{-- VIII. Steps Taken / Remedies --}}
<div class="section">
    <div class="section-title">VIII. Steps Taken / Remedies</div>

    <p>
        Information regarding steps reportedly taken at the domestic level,
        or the reasons why such remedies were unavailable, ineffective, or
        would have posed a risk, is summarized below according to the
        information received.
    </p>

    <p>
        {{ $remedies_exhausted_en }}
    </p>

    <p class="note">
        This information is provided in accordance with the working methods
        of the United Nations Special Rapporteur on the situation of human
        rights defenders.
    </p>
</div>

<div class="section">
    <p>
        This submission is provided in good faith for the purposes of
        international human rights monitoring and falls within the mandate
        of the United Nations Special Rapporteur on the situation of human
        rights defenders.
    </p>

    <p>
        Identifying details of the individual concerned and the source have
        been withheld or anonymized where necessary for protection purposes.
    </p>

    <p class="note">
        Supporting documentation may be provided upon request, subject to
        consent and protection considerations.
    </p>
</div>

</div>
