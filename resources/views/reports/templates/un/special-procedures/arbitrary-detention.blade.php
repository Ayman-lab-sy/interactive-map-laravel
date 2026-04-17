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
        Working Group on Arbitrary Detention
    </p>
    <p>
        <strong>Subject:</strong>
        Alleged arbitrary deprivation of liberty
    </p>
    <p><strong>Case Reference:</strong> {{ $case_number }}</p>
    <p><strong>Date of Submission:</strong> {{ $report_date }}</p>
</div>

<div class="section">
    <p>
        The present submission is respectfully submitted to the
        United Nations Working Group on Arbitrary Detention,
        in accordance with its mandate to investigate cases of
        deprivation of liberty imposed arbitrarily or inconsistently
        with international human rights standards.
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
        of the United Nations Working Group on Arbitrary Detention.
        The information contained herein is submitted strictly as reported,
        without legal qualification or attribution of responsibility.
    </p>
</div>

{{-- I. Summary of Alleged Facts --}}
<div class="section">
    <div class="section-title">I. Summary of Alleged Facts</div>

    <p>
        The following information concerns allegations of deprivation of liberty,
        as reported to a civil society organization. The facts summarized below
        are presented strictly as alleged, without legal qualification or
        attribution of responsibility.
    </p>

    <p>
        The information is presented in a factual manner and, where possible,
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

{{-- II. Information on the Person Deprived of Liberty --}}
<div class="section">
    <div class="section-title">II. Information Concerning the Person Deprived of Liberty</div>

    <p>
        The person reportedly deprived of liberty is described below based on
        the information provided by the source. Certain identifying details may
        be included where relevant, while others have been withheld for
        protection purposes.
    </p>

    <p>
        {{ $victim_information_en }}
    </p>
</div>

{{-- III. Details of the Detention --}}
<div class="section">
    <div class="section-title">III. Details of the Detention</div>

    <p>
        The circumstances surrounding the deprivation of liberty are described
        below, including the reported location of detention, its duration,
        and any information available regarding the authority involved,
        as reported by the source.
    </p>

    <p>
        {{ $detention_details_en }}
    </p>
</div>

{{-- IV. Legal Basis for the Detention --}}
<div class="section">
    <div class="section-title">IV. Legal Basis for the Detention</div>

    <p>
        This section summarizes information regarding whether any legal basis
        for the deprivation of liberty was communicated to the individual,
        as reported by the source.
    </p>

    <p>
        {{ $legal_basis_en }}
    </p>
</div>

{{-- V. Procedural Violations --}}
<div class="section">
    <div class="section-title">V. Procedural Violations</div>

    <p>
        The following information describes any procedural safeguards that were
        reportedly not respected in connection with the deprivation of liberty,
        as reported by the source.
    </p>

    <p>
        {{ $procedural_violations_en }}
    </p>
</div>

{{-- VI. Context / Pattern --}}
<div class="section">
    <div class="section-title">VI. Context / Pattern</div>

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
            deprivation of liberty does not appear to form part of a broader
            or systematic pattern and is presented as an isolated case.
        </p>
    @endif
</div>

{{-- VII. Exhaustion of Remedies --}}
<div class="section">
    <div class="section-title">VII. Exhaustion of Remedies</div>

    <p>
        Information regarding steps taken at the domestic level, or the
        reasons why such remedies were unavailable, ineffective, or would
        have posed a risk, is summarized below according to the information
        received.
    </p>

    <p>
        {{ $remedies_exhausted_en }}
    </p>

    <p class="note">
        This information is provided in accordance with the working methods
        of the United Nations Working Group on Arbitrary Detention.
    </p>
</div>

<div class="section">
    <p>
        This submission is provided in good faith for the purposes of
        international human rights monitoring and falls within the mandate
        of the United Nations Working Group on Arbitrary Detention.
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
