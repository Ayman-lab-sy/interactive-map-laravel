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
        margin-top: 80px; /* أقل بقليل لتوازن الصفحة */
    }

    .report-en .cover h1 {
        font-size: 18px;   /* كان كبيرًا زيادة */
        font-weight: bold;
        margin-bottom: 8px;
    }

    .report-en .cover h2 {
        font-size: 16px;   /* أخف من العنوان الرئيسي */
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

    .report-en table td,
    .report-en table th {
        border: 1px solid #000;
        padding: 8px;
        vertical-align: top;
    }

    .report-en .page-break {
        page-break-after: always;
    }

    .report-en .note {
        font-size: 13px;
        font-style: italic;
        margin-top: 15px;
        page-break-inside: auto;
    }

    .report-en p {
        margin: 8px 0;
        orphans: 2;
        widows: 2;
    }

    .report-en .section {
        break-inside: auto;
        page-break-inside: auto;
        margin-bottom: 24px;
    }

    .report-en .section.keep-together {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .org-logo {
        width: 90px;
        margin-bottom: 15px;
    }


</style>

<!-- Cover Page -->
<div class="cover">

    <img src="logo.png"
         alt="Syrian Alawites and Minorities Organization"
         class="org-logo">

    <h1>Syrian Alawites and Minorities Organization</h1>

    <h2>NGO Legal Documentation Report – Human Rights Case</h2>

    <p><strong>Case Reference:</strong> {{ $case_number }}</p>
    <p><strong>Date of Issue:</strong> {{ $report_date }}</p>
    <p>
        <strong>Confidentiality Level:</strong>
        Strictly Confidential – For authorized international human rights organizations only
    </p>

    <p class="note">
        This report documents a reported human rights–related incident submitted to the Syrian Alawites and Minorities Organization,
        in accordance with internal documentation procedures based on principles of neutrality, accuracy, and victim protection.
        <br><br>
        The report has been prepared exclusively for human rights documentation, analysis, and potential advocacy purposes,
        without making any legal determination, accusation, or attribution of responsibility.
        <br><br>
        <strong>Confidentiality Notice:</strong><br>
        This report contains sensitive information and must not be disclosed, reproduced, or circulated beyond authorized recipients,
        in line with established confidentiality, privacy, and victim protection standards.
    </p>
</div>

<div class="page-break"></div>

<!-- Section 2: Case Summary (Prepared by the Organization) -->
<div class="section-title">Case Summary (Prepared by the Organization)</div>

<p>
Following an internal editorial review conducted by the Syrian Alawites and Minorities Organization,
this case concerns an alleged human rights–related incident reported by one or more affected individuals.
The information summarized below reflects the Organization’s structured understanding of the case,
prepared for documentation, monitoring, and potential advocacy purposes.
</p>

<p>
The case was assessed within the Organization’s internal case documentation framework,
which aims to ensure consistency, contextual accuracy, and protection of the concerned individual(s),
while maintaining strict neutrality and avoiding premature legal qualification or attribution of responsibility.
</p>

<p>
The reported incident occurred within a broader context characterized by heightened administrative
and security-related constraints, which may increase the vulnerability of affected individuals
and limit available avenues for protection or redress. This contextual environment has been taken
into account for documentation purposes, without reference to specific actors or attribution of intent.
</p>

<table>
    <tr>
        <td><strong>Geographic Location</strong></td>
        <td>{{ $general_location }}</td>
    </tr>
    <tr>
        <td><strong>Incident Timeframe</strong></td>
        <td>{{ $incident_timeframe }}</td>
    </tr>
    <tr>
        <td><strong>Nature of the Reported Incident</strong></td>
        <td>{{ $violation_summary }}</td>
    </tr>
    <tr>
        <td><strong>Reported Psychosocial Impact</strong></td>
        <td>{{ $psychosocial_impact_text }}</td>
    </tr>
</table>

<p>
Based on the available information, the reported circumstances may indicate increased vulnerability for the affected individual(s), particularly in the absence of effective remedies
or protective safeguards. No assessment of imminent risk has been conducted, and this observation
is included solely for contextual and documentation purposes.
</p>

<p class="note">
This summary has been prepared by the Organization for contextual and documentation purposes.
It does not reproduce the source’s account verbatim and should not be interpreted as a legal finding,
judicial assessment, or determination of responsibility.
</p>


<!-- Section 3: Source Account (As Reported) -->
<div class="section-title">Source Account (As Reported)</div>

<p>
The following section presents the account provided by the reporting source(s) regarding
the alleged incident. The information is reproduced as submitted to the Syrian Alawites and
Minorities Organization and reflects the source’s description of events, circumstances,
and perceived impacts.
</p>

<p>
The Organization has not conducted independent verification of the details contained in
this account, nor has it carried out investigative or judicial procedures. The narrative
is included in this report strictly for documentation purposes and to preserve the source’s
testimony within a structured human rights documentation framework.
</p>

<p>
Care has been taken to document the account in a manner that respects the dignity, safety,
and privacy of the concerned individual(s), including the omission or generalization of
identifying details where necessary.
</p>

<p>{{ $legal_narrative }}</p>

<p class="note">
This section reflects the source’s account as reported. Its inclusion does not imply factual
verification, legal qualification, or attribution of responsibility by the Organization.
</p>

<div class="page-break"></div>

<!-- Section 4: Applicable Human Rights Framework -->
<div class="section">
  <div class="section-title">Applicable Human Rights Framework</div>

  <p>
    From a general human rights perspective, the circumstances described in this report may
    raise concerns under internationally recognized human rights principles related to the
    protection of personal security, the right to physical and psychological integrity, and
    the obligation to prevent threats, intimidation, or degrading treatment.
  </p>

  <p>
    These principles are reflected across widely accepted international human rights standards
    and monitoring practices, which emphasize the duty to respect and protect individuals from
    acts that may undermine their safety, dignity, or well-being, particularly in contexts
    characterized by heightened vulnerability.
  </p>

  <p>
    The inclusion of this framework is intended to provide contextual understanding of the
    reported information and to situate the case within broader human rights considerations,
    without asserting a definitive legal classification or attributing responsibility to any
    party.
  </p>

  <p class="note">
    This section is provided for analytical and contextual purposes only. Any legal assessment,
    qualification, or determination of responsibility remains within the mandate of competent
    national or international authorities.
  </p>
</div>


<!-- Section 5: Supporting Materials -->
<div class="section">
  <div class="section-title">Supporting Materials and Documentation</div>

  <p>
    The case file includes supporting materials submitted by the reporting source(s) for
    documentation purposes and to enhance contextual understanding of the reported incident.
    These materials are intended to complement the documented information and provide
    additional insight into the circumstances described in this report.
  </p>

  <p>
    Where available, supporting materials may include visual, documentary, or written elements
    relevant to the reported facts. Detailed contextual descriptions, original formats, and
    associated metadata are retained within the Organization’s internal records, in accordance
    with established documentation and data protection procedures.
  </p>

  <table>
    <tr>
      <th>#</th>
      <th>Evidence Type</th>
      <th>Description</th>
      <th>Date Added</th>
    </tr>
    {!! $evidence_rows !!}
  </table>

  <p class="note">
    No independent verification, forensic analysis, or authentication of the submitted
    materials has been conducted unless explicitly stated. The materials are included strictly
    for documentation and reference purposes and do not constitute proof or legal evidence.
  </p>
</div>

<div class="section">
  <!-- Section 7: Documentation Status and Internal Review -->
  <div class="section-title">Documentation Status and Internal Review</div>

  <p><strong>Internal Verification Level:</strong> {{ $verification_level_label }}</p>
  <p><strong>Review Summary:</strong> {{ $review_summary }}</p>
  <p><strong>Date of Review:</strong> {{ $review_date }}</p>

  <p class="note">
  The internal review process does not constitute an investigation or judicial assessment
  and should not be interpreted as a determination of facts or liability.
  </p>
</div>

<div class="section keep-together">
  <!-- Section: Conclusion -->
  <div class="section-title">Conclusion</div>

  <p>
  This report provides structured and neutral documentation of a reported human rights–related
  incident submitted to the Syrian Alawites and Minorities Organization. It has been prepared
  in accordance with internal documentation standards grounded in accuracy, neutrality, and
  the protection of affected individuals.
  </p>

  <p>
  The information contained herein is presented to support the work of international human
  rights organizations within their respective mandates, including monitoring, analysis,
  and potential follow-up, as deemed appropriate by the receiving entity.
  </p>

  <p>
  The Organization remains committed to responsible documentation practices and stands ready
  to provide additional contextual information, subject to consent, confidentiality
  considerations, and its institutional mandate.
  </p>

  <p class="note">
  This report does not constitute a legal determination, judicial assessment, or attribution
  of responsibility. Any subsequent action or evaluation remains at the discretion of the
  receiving organization and relevant competent authorities.
  </p>
</div>

</div>