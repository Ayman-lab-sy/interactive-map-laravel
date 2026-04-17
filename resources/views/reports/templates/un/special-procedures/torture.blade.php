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
    <strong>Mandate:</strong> Special Rapporteur on torture and other cruel, inhuman or degrading treatment or punishment
    </p>
    <p><strong>Subject:</strong> Allegations of torture and other cruel, inhuman or degrading treatment or punishment</p>
    <p><strong>Case Reference:</strong> {{ $case_number }}</p>
    <p><strong>Date of Submission:</strong> {{ $report_date }}</p>
</div>

{{-- Section 1: Summary of Alleged Facts --}}
<div class="section">
    <div class="section-title">I. Summary of Alleged Facts</div>

    <p>
        The following information concerns allegations of acts that may amount to torture or other cruel,
        inhuman, or degrading treatment or punishment, as reported to a civil society organization.
        The facts summarized below are presented strictly as alleged, without any legal qualification
        or attribution of responsibility.
    </p>

    <p>
    The information below reflects a factual summary of the alleged events as reported, presented in chronological order where possible.
    </p>

    <p>
        {{ $incident_summary_en }}
    </p>

    <p class="note">
        This summary reflects the information as reported. Its inclusion does not imply factual verification
        or legal assessment by the submitting organization.
    </p>
</div>

{{-- Section 2: Victim Profile --}}
<div class="section">
    <div class="section-title">II. Information concerning the victim(s)</div>

    <p>
        The alleged victim can be described as follows, based on the information provided.
        Identifying details have been omitted or generalized for protection purposes.
    </p>

    <p>
        {{ $victim_profile_en }}
    </p>
</div>

{{-- Section 3: Alleged Perpetrators --}}
<div class="section">
    <div class="section-title">III. Information concerning the alleged perpetrators</div>

    <p class="note">
    The identification of alleged perpetrators is based solely on information provided by the source and does not imply any determination of responsibility.
    </p>

    <p>
        According to the information received, the alleged acts are attributed to the following
        actors or entities, described in general terms.
    </p>

    <p>
        {{ $alleged_perpetrators_en }}
    </p>
</div>

{{-- Section 4: Context and Pattern --}}
@if(!empty($context_pattern_en))
<div class="section">
    <div class="section-title">IV. Context and pattern of alleged violations</div>

    <p>
        The alleged incident may have occurred within a broader context or pattern, as described below.
        This information is provided for contextual understanding only.
    </p>

    <p>
        {{ $context_pattern_en }}
    </p>
</div>
@endif

{{-- Section 5: Exhaustion of Domestic Remedies --}}
<div class="section">
    <div class="section-title">V. Information on exhaustion of domestic remedies</div>

    <p>
        Information regarding the availability, use, or effectiveness of domestic remedies
        in relation to the alleged acts is summarized below.
    </p>

    <p>
        {{ $remedies_exhausted_en }}
    </p>

    <p class="note">
        This information is provided in accordance with the requirements commonly applied
        under the Special Procedures mandate.
    </p>
</div>

{{-- Section 6: Procedural Information --}}
<div class="section">
    <div class="section-title">VI. Procedural Information</div>

    <p>
        The submitting organization confirms that this information is provided in accordance
        with the working methods of the United Nations Special Procedures.
    </p>

    <p>
        The identity of the source and any identifying information related to the victim(s)
        have been withheld or anonymized for protection purposes.
    </p>
</div>


{{-- Closing --}}
<div class="section">
    <p>
        This submission is provided in good faith for the purposes of international human rights
        monitoring and falls within the mandate of the United Nations Special Procedures.
    </p>

    <p>
        The submitting organization requests that this information be treated confidentially
        and that no steps be taken that could expose the victim(s) or source(s) to risk.
    </p>

    <p class="note">
        Supporting documentation is available and may be transmitted upon request,
        subject to consent and protection considerations.
    </p>
</div>

</div>
