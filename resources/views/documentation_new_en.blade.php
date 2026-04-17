@extends('layouts.main')

@section('title', 'Report Human Rights Violation Securely | Syrian Minorities Organization')

@section('meta')

@php
$description = "Secure and confidential platform to report human rights violations in Syria including arbitrary detention, enforced disappearance, torture, discrimination, forced displacement, and other abuses affecting Syrian minorities.";
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="Report Human Rights Violation Securely">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">
<meta property="og:site_name" content="Organization of Alawites and Syrian Minorities for Justice and Peace">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="Report Human Rights Violation Securely">
<meta name="twitter:description" content="{{ $description }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "Report Human Rights Violation",
  "description": "{{ $description }}",
  "url": "{{ request()->fullUrl() }}",
  "publisher": {
    "@type": "Organization",
    "name": "Organization of Alawites and Syrian Minorities for Justice and Peace"
  }
}
</script>

@endsection

@section('content')

<header class="hero">
  <img src="{{ asset('assets/logo.png') }}" alt="Organization Logo" class="logo">
  <h1>Report a Human Rights Violation Securely</h1>
  <p>Share the details of the case you experienced so it can be documented within the organization’s records and advocated for.</p>
  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">Back to Homepage</a>
  </div>
</header>

<div class="form-container" style="background:#eef2ff; border:2px solid #6366f1; text-align:center;">

  <h3 style="margin-bottom:10px;">⚠️ Before you begin</h3>

  <p style="margin-bottom:15px;">
   If you feel scared or hesitant, that's normal.  
   You can submit your status anonymously; all information will be treated with the utmost confidentiality.
  </p>

  <div style="margin-bottom:15px; color:#059669;">
    ✔ You can use a pseudonym
    ✔ You can leave some fields blank. 
    ✔ You can only send the minimum amount of information. 
  </div>

  <p style="font-size:14px; color:#444;">
   No information will be shared without your consent.
  </p>

</div>

<main class="main-content container">
  <div class="form-container"
       style="
          margin-bottom:25px;
          border:2px dashed #f1c40f;
          background:#fffbe6;
          display:flex;
          flex-direction:column;
          align-items:center;
          text-align:center;
       ">

      <p style="font-weight:bold; margin-bottom:8px;">
          🔔 Do you already have a registered case?
      </p>

      <p style="margin-bottom:16px;">
          You can add new information or evidence using your
          <strong>Case Number</strong> and <strong>Follow-up Code</strong>.
      </p>

      <a href="{{ url(app()->getLocale().'/documentation/follow-up') }}"
         class="btn btn-outline">
          ➕ Add information to an existing case
      </a>

  </div>

  <div class="form-container" style="border:2px solid #2ecc71; background:#f6fffa;">
    <h3 style="margin-bottom:10px;">🔒 Privacy & Security</h3>

    <p>
      This form is <strong>fully secure and confidential</strong>.
      All submitted data is stored in a protected system and is not shared with any external party
      <strong>without your explicit and prior consent</strong>.
    </p>

    <p>
      You may report:
      <strong>using your real name</strong>,
      <strong>using an alias</strong>, or
      <strong>without providing any sensitive identifying information</strong>.
    </p>

    <p>
      Sharing information with international or human rights entities only occurs if you choose so,
      and your real name will never be disclosed in any case.
    </p>
  </div>

  <div class="form-container">
    <h2>📄 Case Documentation Form</h2>

    <p class="form-note">
      <span style="color:red;">*</span>
      Fields marked with a red asterisk are mandatory to ensure proper documentation of the case.
    </p>

    <form action="{{ route('case.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      @if ($errors->any())
        <div class="form-note" style="border-color:#e74c3c; background:#fdecea;">
          <strong>⚠️ The form was not submitted</strong>
          <p>Please correct the errors listed below and try again.</p>
        </div>
      @endif

      <div class="form-group">
        <label>Type of name used:
          <span class="required">*</span>
        </label>
        <select name="name_type" required>
          <option value="real" {{ old('name_type') == 'real' ? 'selected' : '' }}>Real name</option>
          <option value="alias" {{ old('name_type','alias') == 'alias' ? 'selected' : '' }}>Alias</option>
        </select>
        @error('full_name') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>Full name or alias:
          <span class="required">*</span>
        </label>
        <small class="hint">
          You may enter your real name, an alias, or any name you feel safe using.
        </small>
        <input type="text" name="full_name" value="{{ old('full_name') }}">

        @error('full_name')
          <small style="color:#c0392b;">{{ $message }}</small>
        @enderror
      </div>

      <div class="form-group">
        <label>Date of birth:</label>
        <input type="date" name="birth_date" value="{{ old('birth_date') }}">
      </div>

      <div class="form-group">
          <label>Religious or Ethnic Community:</label>
          <select name="component" class="form-control" required>
              <option value="">--Select Religious or Ethnic Community--</option>
              <option value="ALAWITE" {{ old('component') == 'ALAWITE' ? 'selected' : '' }}>ALAWITE</option>
              <option value="SUNNI" {{ old('component') == 'SUNNI' ? 'selected' : '' }}>SUNNI</option>
              <option value="SHIA" {{ old('component') == 'SHIA' ? 'selected' : '' }}>SHIA</option>
              <option value="ISMAILI" {{ old('component') == 'ISMAILI' ? 'selected' : '' }}>ISMAILI</option>
              <option value="DRUZE" {{ old('component') == 'DRUZE' ? 'selected' : '' }}>DRUZE</option>
              <option value="Murshidi" {{ old('component') == 'Murshidi' ? 'selected' : '' }}>Murshidi</option>
              <option value="CHRISTIAN" {{ old('component') == 'CHRISTIAN' ? 'selected' : '' }}>CHRISTIAN</option>
              <option value="KURD" {{ old('component') == 'KURD' ? 'selected' : '' }}>KURD</option>
              <option value="TURKMEN" {{ old('component') == 'TURKMEN' ? 'selected' : '' }}>TURKMEN</option>
              <option value="CIRCASSIAN" {{ old('component') == 'CIRCASSIAN' ? 'selected' : '' }}>CIRCASSIAN</option>
              <option value="ARMENIAN" {{ old('component') == 'ARMENIAN' ? 'selected' : '' }}>ARMENIAN</option>
              <option value="ASSYRIAN_CHALDEAN" {{ old('component') == 'ASSYRIAN_CHALDEAN' ? 'selected' : '' }}>ASSYRIAN/CHALDEAN</option>
              <option value="OTHER" {{ old('component') == 'OTHER' ? 'selected' : '' }}>OTHER</option>
          </select>
      </div>

      <div class="form-group">
        <label>Place of residence (Country / City):
          <span class="required">*</span>
        </label>
        <small class="hint">
          Enter your current country and city, or your last place of residence if displaced.
        </small>
        <input type="text" name="location" value="{{ old('location') }}">
        @error('location') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>Phone number / WhatsApp:</label>
        <input type="tel" name="phone" placeholder="Example: +963xxxxxxxxx" value="{{ old('phone') }}">
      </div>

      <div class="form-group">
        <label>Email address:
          <span class="required">*</span>
        </label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>Spouse name:</label>
        <input type="text" name="spouse_name" value="{{ old('spouse_name') }}">
      </div>

      <div class="form-group">
        <label>Number of children, their names and ages:</label>

        @php
          $oldNames = old('children_names', ['']);
          $oldAges  = old('children_ages', ['']);
        @endphp

        <div id="children-container">
          @foreach($oldNames as $index => $name)
            <div class="child-entry">
              <input type="text"
                     name="children_names[]"
                     placeholder="Child name"
                     value="{{ $name }}">

              <input type="number"
                     name="children_ages[]"
                     placeholder="Age"
                     value="{{ $oldAges[$index] ?? '' }}">
           </div>
          @endforeach
        </div>

        <button type="button"
                class="add-child-btn"
                onclick="addChildEntry()">
          ➕ Add another child
        </button>
      </div>

      <div class="form-group">
        <label>Is there a direct threat?</label>
        <select name="direct_threat">
          <option value="1" {{ old('direct_threat') == '1' ? 'selected' : '' }}>Yes</option>
          <option value="0" {{ old('direct_threat') == '0' ? 'selected' : '' }}>No</option>
        </select>
      </div>

      <div class="form-group">
        <label>Type of violation:
          <span class="required">*</span>
        </label>
        <select name="violation_type" required>
          <option value="">-- Select --</option>
          <option value="arbitrary_detention" {{ old('violation_type') == 'arbitrary_detention' ? 'selected' : '' }}>Arbitrary detention</option>
          <option value="enforced_disappearance" {{ old('violation_type') == 'enforced_disappearance' ? 'selected' : '' }}>Enforced disappearance</option>
          <option value="torture" {{ old('violation_type') == 'torture' ? 'selected' : '' }}>Torture or cruel treatment</option>
          <option value="threat" {{ old('violation_type') == 'threat' ? 'selected' : '' }}>Threat or intimidation</option>
          <option value="discrimination" {{ old('violation_type') == 'discrimination' ? 'selected' : '' }}>Religious or ethnic discrimination</option>
          <option value="sexual_violence" {{ old('violation_type') == 'sexual_violence' ? 'selected' : '' }}>Sexual or gender-based violence</option>
          <option value="property_violation" {{ old('violation_type') == 'property_violation' ? 'selected' : '' }}>Property confiscation or destruction</option>
          <option value="forced_displacement" {{ old('violation_type') == 'forced_displacement' ? 'selected' : '' }}>Forced displacement</option>
          <option value="other" {{ old('violation_type') == 'other' ? 'selected' : '' }}>Other</option>
        </select>
        @error('violation_type') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>Description of the threat or violation:
          <span class="required">*</span>
        </label>
        <small class="hint">
          Describe what happened in detail: what occurred, when, who was affected, and how it impacted you or your family.
          Write freely in your own words.
        </small>
        <textarea name="threat_description" rows="4">{{ old('threat_description') }}</textarea>
        @error('threat_description') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>Responsible party for the threat (if known):</label>
        <input type="text" name="threat_source" value="{{ old('threat_source') }}">
      </div>

      <div class="form-group">
        <label>Date or period of the threat:</label>
        <input type="date" name="threat_date" value="{{ old('threat_date') }}">
      </div>

      <div class="form-group">
        <label>Locations where violations occurred (if any):</label>
        <input type="text" name="threat_locations" value="{{ old('threat_locations') }}">
      </div>

      <div class="form-group">
        <label>Are there individuals suffering psychological or health impacts?</label>
        <select name="psychological_impact">
          <option value="1">Yes</option>
          <option value="0">No</option>
        </select>
      </div>

      <div class="form-group">
        <label>Additional psychological or social impact details:</label>
        <small class="hint">
          You may explain any psychological or social consequences such as persistent fear, anxiety,
          depression, job loss, social isolation, or family difficulties.
          This field is optional and used only to better understand your situation.
        </small>
        <textarea name="impact_details" rows="3">{{ old('impact_details') }}</textarea>
      </div>

      <div class="form-group">
        <label>Do you believe this case is part of a recurring pattern?</label>
        <select name="is_pattern_case" required>
          <option value="0" {{ old('is_pattern_case') == '0' ? 'selected' : '' }}>No</option>
          <option value="1" {{ old('is_pattern_case') == '1' ? 'selected' : '' }}>Yes</option>
        </select>
      </div>

      <div class="form-group">
        <label>Case sensitivity level:
          <span class="required">*</span>
        </label>
        <select name="case_sensitivity" required>
          <option value="low" {{ old('case_sensitivity') == 'low' ? 'selected' : '' }}>Low</option>
          <option value="medium" {{ old('case_sensitivity') == 'medium' ? 'selected' : '' }}>Medium</option>
          <option value="high" {{ old('case_sensitivity') == 'high' ? 'selected' : '' }}>High</option>
        </select>
        @error('case_sensitivity') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>Documents and evidence (photos, reports, etc.):</label>
        <small class="hint">
          You may upload photos, reports, messages, or any file supporting your account.
          File upload is optional and can be added later.
        </small>
        <input type="file" name="documents[]" multiple>
      </div>

      <div class="form-group" style="background:#f9f9f9; padding:10px;">
        <small>
          ⚠️ <strong>Important notice:</strong><br>
          The following consents are optional (except case documentation),
          and can be withdrawn at any time through the organization’s team.
        </small>
      </div>

      <div class="form-check">
        <label>
          <input type="checkbox" name="agreed_to_document" {{ old('agreed_to_document') ? 'checked' : '' }} required>
          I agree to document this case within the organization’s records. <span class="required">*</span>
        </label>
        @error('agreed_to_document') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>

      <div class="form-check">
        <label><input type="checkbox" name="agreed_to_share"> I agree to share my case with external human rights organizations (without mentioning my real name).</label>
      </div>

      <div class="form-check">
        <label><input type="checkbox" name="agreed_to_campaign"> I agree to use my story in advocacy reports or campaigns (non-public).</label>
      </div>

      <div class="form-note">
        ⚠️ After submitting the case, you will receive a <strong>Case Number</strong> and a <strong>Follow-up Code</strong>.<br>
        Please keep them to add any future information.
      </div>

      <div style="text-align: center;">
        <button type="submit" class="btn btn-outline">📨 Submit Case securely</button>
      </div>
    </form>
  </div>
</main>

<script>
  function addChildEntry() {
    const container = document.getElementById('children-container');

    const wrapper = document.createElement('div');
    wrapper.className = 'child-entry';

    const nameInput = document.createElement('input');
    nameInput.type = 'text';
    nameInput.name = 'children_names[]';
    nameInput.placeholder = 'Child name';

    const ageInput = document.createElement('input');
    ageInput.type = 'number';
    ageInput.name = 'children_ages[]';
    ageInput.placeholder = 'Age';

    wrapper.appendChild(nameInput);
    wrapper.appendChild(ageInput);

    container.appendChild(wrapper);
  }
</script>

<style>
  .required {
    color: red;
    font-weight: bold;
    margin-right: 4px;
  }

  .hint {
    display: block;
    font-size: 0.85em;
    color: #666;
    margin-bottom: 6px;
  }

  .form-note {
    background: #fff8e1;
    border: 1px solid #f1c40f;
    padding: 10px;
    margin-bottom: 20px;
    font-size: 0.9em;
  }
</style>

@endsection
