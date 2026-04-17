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
</style>

{{-- Header --}}
<div class="section">
    <p><strong>To:</strong> United Nations Special Procedures</p>
    <p>
        <strong>Mandate:</strong>
        Special Rapporteur on Minority Issues
    </p>
    <p>
        <strong>Subject:</strong>
        Alleged violations affecting persons belonging to minorities
    </p>
    <p><strong>Case Reference:</strong> {{ $case_number }}</p>
    <p><strong>Date of Submission:</strong> {{ $report_date }}</p>
</div>

<div class="section">
    <p>
        The present submission is respectfully submitted to the
        United Nations Special Rapporteur on Minority Issues, in accordance
        with the mandate to examine, promote, and protect the rights of
        persons belonging to national, ethnic, religious, and linguistic
        minorities.
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
        of the United Nations Special Rapporteur on Minority Issues. The
        information is submitted strictly as reported, without legal
        qualification or attribution of responsibility.
    </p>
</div>

{{-- I. Summary of Alleged Facts --}}
<div class="section">
    <div class="section-title">I. Summary of Alleged Facts</div>

    <p>
        The following information concerns allegations of acts or measures
        reportedly affecting individuals belonging to minority groups. The
        facts summarized below are presented strictly as alleged, without
        legal qualification or attribution of responsibility.
    </p>

    <p>
        {{ $incident_summary_en }}
    </p>

    <p class="note">
        This summary reflects the information as reported and does not imply
        verification or legal assessment by the submitting organization.
    </p>
</div>

{{-- II. Information on the Individual(s) --}}
<div class="section">
    <div class="section-title">II. Information on the Individual(s)</div>

    <p>
        The individuals concerned are described below, based on the
        information provided by the source. Certain identifying details may
        be included where relevant, while others have been withheld for
        protection purposes.
    </p>

    <p>
        {{ $victim_information_en }}
    </p>
</div>

{{-- III. Minority Identity --}}
<div class="section">
    <div class="section-title">III. Minority Identity</div>

    <p>
        This section outlines the minority identity relevant to the reported
        allegations, including ethnic, national, linguistic, or religious
        affiliation, as described by the source.
    </p>

    <p>
        {{ $minority_or_religious_identity_en }}
    </p>
</div>

{{-- IV. Nature of the Alleged Violations --}}
<div class="section">
    <div class="section-title">IV. Nature of the Alleged Violations</div>

    <p>
        The following information summarizes the acts or measures reportedly
        experienced by the individuals concerned, which are alleged to have
        adversely affected their rights as persons belonging to minorities.
    </p>

    <p>
        {{ $violation_description_en }}
    </p>
</div>

{{-- V. Alleged Perpetrators --}}
<div class="section">
    <div class="section-title">V. Alleged Perpetrators</div>

    <p>
        This section presents information regarding the individuals,
        authorities, or entities that are reportedly involved in the
        alleged acts, as described by the source.
    </p>

    <p>
        {{ $alleged_perpetrators_en }}
    </p>
</div>

{{-- VI. Context / Pattern --}}
<div class="section">
    <div class="section-title">VI. Context / Pattern</div>

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

{{-- VII. Steps Taken / Remedies --}}
<div class="section">
    <div class="section-title">VII. Steps Taken / Remedies</div>

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
        of the United Nations Special Rapporteur on Minority Issues.
    </p>
</div>

<div class="section">
    <p>
        This submission is provided in good faith for the purposes of
        international human rights monitoring and falls within the mandate
        of the United Nations Special Rapporteur on Minority Issues.
    </p>

    <p>
        Identifying details of the individuals concerned and the source have
        been withheld or anonymized where necessary for protection purposes.
    </p>

    <p class="note">
        Supporting documentation may be provided upon request, subject to
        consent and protection considerations.
    </p>
</div>

</div>
