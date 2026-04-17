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
        Special Rapporteur on the promotion and protection of the right to freedom of opinion and expression
    </p>
    <p>
        <strong>Subject:</strong>
        Alleged restrictions on freedom of opinion and expression
    </p>
    <p><strong>Case Reference:</strong> {{ $case_number }}</p>
    <p><strong>Date of Submission:</strong> {{ $report_date }}</p>
</div>

<div class="section">
    <p>
        The present submission is respectfully submitted to the
        United Nations Special Rapporteur on the promotion and protection
        of the right to freedom of opinion and expression,
        in accordance with the mandate to identify, examine, and advise
        on situations involving alleged restrictions on this right.
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
        of the United Nations Special Rapporteur on freedom of opinion and
        expression. The information contained herein is submitted strictly
        as reported, without legal qualification or attribution of
        responsibility.
    </p>
</div>

{{-- I. Summary of Alleged Facts --}}
<div class="section">
    <div class="section-title">I. Summary of Alleged Facts</div>

    <p>
        The following information concerns allegations of restrictions or
        interference with the exercise of the right to freedom of opinion
        and expression, as reported to a civil society organization.
        The facts summarized below are presented strictly as alleged,
        without legal qualification or attribution of responsibility.
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

{{-- II. Information on the Individual --}}
<div class="section">
    <div class="section-title">II. Information on the Individual Concerned</div>

    <p>
        The individual whose right to freedom of opinion or expression was
        reportedly affected is described below, based on the information
        provided by the source. Certain identifying details may be included
        where relevant, while others have been withheld for protection
        purposes.
    </p>

    <p>
        {{ $victim_information_en }}
    </p>
</div>

{{-- III. Description of the Expression or Activity --}}
<div class="section">
    <div class="section-title">III. Description of the Expression or Activity</div>

    <p>
        This section describes the nature of the opinion, expression,
        publication, communication, or activity that was reportedly
        restricted, interfered with, or sanctioned, as reported by the
        source.
    </p>

    <p>
        {{ $expression_activity_en }}
    </p>
</div>

{{-- IV. Alleged Restrictions or Violations --}}
<div class="section">
    <div class="section-title">IV. Alleged Restrictions or Interference</div>

    <p>
        The following information summarizes the manner in which the
        expression or activity described above was reportedly restricted,
        interfered with, or sanctioned, including any measures such as
        arrest, detention, threats, censorship, content removal, or other
        forms of pressure, as reported by the source.
    </p>

    <p>
        {{ $violations_details_en }}
    </p>
</div>

{{-- V. Legal Context --}}
<div class="section">
    <div class="section-title">V. Legal Context</div>

    <p>
        This section summarizes any information regarding whether a legal
        basis, law, regulation, or official justification was reportedly
        cited in connection with the restriction or interference, as
        reported by the source.
    </p>

    <p>
        {{ $legal_basis_en }}
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
            restriction or interference does not appear to form part of a
            broader or systematic pattern and is presented as an isolated
            case.
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
        of the United Nations Special Rapporteur on freedom of opinion and
        expression.
    </p>
</div>

<div class="section">
    <p>
        This submission is provided in good faith for the purposes of
        international human rights monitoring and falls within the mandate
        of the United Nations Special Rapporteur on the promotion and
        protection of the right to freedom of opinion and expression.
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
