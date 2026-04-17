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
        Working Group on Enforced or Involuntary Disappearances
    </p>
    <p>
        <strong>Subject:</strong>
        Alleged enforced or involuntary disappearance
    </p>
    <p><strong>Case Reference:</strong> {{ $case_number }}</p>
    <p><strong>Date of Submission:</strong> {{ $report_date }}</p>
</div>


<div class="section">
    <p>
        The present submission is respectfully submitted to the
        United Nations Working Group on Enforced or Involuntary Disappearances,
        in accordance with its mandate to assist families in determining the fate
        or whereabouts of disappeared persons and to monitor patterns of enforced
        or involuntary disappearances worldwide.
    </p>

    <p>
        The information contained herein has been received and documented by a civil
        society source and is submitted in good faith for the purposes of international
        human rights monitoring. Identifying details have been handled with due regard
        to safety and protection considerations.
    </p>

    <p>
        This submission is presented in accordance with the working methods of the
        United Nations Working Group on Enforced or Involuntary Disappearances.
        The information contained herein is submitted strictly as reported,
        without legal qualification or attribution of responsibility.
    </p>

</div>

{{-- I. Summary of Alleged Facts --}}
<div class="section">
    <div class="section-title">I. Summary of Alleged Facts</div>

    <p>
        The following information concerns allegations of deprivation of liberty
        followed by concealment of the fate or whereabouts of a person, as reported
        to a civil society organization. The facts summarized below are presented
        strictly as alleged, without legal qualification or attribution of responsibility.
    </p>

    <p>
        The information is presented in a factual manner and, where possible,
        in chronological order, based solely on the account received.
    </p>

    <p>
        {{ $incident_summary_en }}
    </p>

    <p class="note">
        This summary reflects the information as reported. Its inclusion does not imply
        verification or legal assessment by the submitting organization.
    </p>
</div>


{{-- II. Information on the Victim --}}
<div class="section">
    <div class="section-title">II. Information Concerning the Disappeared Person</div>

    <p>
        The disappeared person is described below based on the information provided
        by the source. Certain identifying details may be included where relevant,
        while others have been withheld for protection purposes.
    </p>

    <p>
        {{ $victim_information_en }}
    </p>
</div>

{{-- III. Circumstances of the Disappearance --}}
<div class="section">
    <div class="section-title">III. Circumstances of the Disappearance</div>

    <p>
        The circumstances surrounding the deprivation of liberty and the subsequent
        disappearance of the individual are described below, as reported by the source.
    </p>

    <p>{{ $circumstances_en }}</p>
</div>

{{-- IV. Alleged Perpetrators --}}
<div class="section">
    <div class="section-title">IV. Alleged Responsible Actors</div>
    <p class="note">
        The identification of alleged responsible actors is based solely on the information
        provided by the source and does not imply any determination of responsibility.
    </p>

    <p>
        {{ $alleged_perpetrators_en }}
    </p>

</div>

{{-- V. Context / Pattern --}}
<div class="section">
    <div class="section-title">V. Context / Pattern</div>
    <p>
        The following information is provided for contextual purposes only and does not
        seek to establish the existence of a systematic policy or practice.
    </p>

    @if(!empty($context_pattern_en))
        <p>{{ $context_pattern_en }}</p>
    @else
        <p>
            Based on the information available to the source, the reported incident
            does not appear to form part of a broader or systematic pattern of enforced
            or involuntary disappearances and is presented as an isolated case.
        </p>
    @endif
</div>


{{-- VI. Steps Taken / Exhaustion of Remedies --}}
<div class="section">
    <div class="section-title">VI. Steps Taken at the Domestic Level</div>
    <p>
        Information regarding steps taken at the domestic level, or the
        reasons why such remedies were unavailable or ineffective, is
        summarized below according to the information received.
    </p>

    <p>{{ $steps_taken_en }}</p>
    <p class="note">
        This information is provided in accordance with the working methods of the
        United Nations Working Group on Enforced or Involuntary Disappearances.
    </p>
</div>

<div class="section">
    <p>
        This submission is provided in good faith for the purposes of international
        human rights monitoring and falls within the mandate of the United Nations
        Working Group on Enforced or Involuntary Disappearances.
    </p>

    <p>
        Identifying details of the disappeared person and the source have been
        withheld or anonymized where necessary for protection purposes.
    </p>

    <p class="note">
        Supporting documentation may be provided upon request, subject to consent
        and protection considerations.
    </p>
</div>

</div>