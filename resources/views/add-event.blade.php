<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: Arial;
            background: #111;
            color: #fff;
            padding: 30px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            background: #1e1e1e;
            border: 1px solid #333;
            color: #fff;
        }

        button {
            padding: 10px 20px;
            background: #00d4ff;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .delete-btn {
            background: red;
            color: #fff;
            margin-top: 10px;
        }
    </style>
</head>

<body>

<a href="{{ url(app()->getLocale() . '/map') }}" style="display:inline-block; margin-bottom:15px; color:#00d4ff;">
    ← الرجوع إلى الخريطة
</a>

{{-- العنوان --}}
@if(isset($event))
  <h2>تعديل الحدث</h2>
@else
  <h2>إضافة حدث جديد</h2>
@endif

@if ($errors->any())
    <div style="background:#7f1d1d; padding:10px; margin-bottom:15px; border-radius:8px;">
        <ul style="margin:0; padding-right:20px;">
            @foreach ($errors->all() as $error)
                <li style="color:#fecaca;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- رسالة نجاح --}}
@if(session('success'))
    <div style="background:#0f5132; padding:10px; margin-bottom:15px;">
        {{ session('success') }}
    </div>
@endif

{{-- ========================= --}}
{{-- 🔵 فورم الإضافة / التعديل --}}
{{-- ========================= --}}
@if(isset($event))
<form method="POST" action="{{ url(app()->getLocale() . '/update-event/' . $event->id) }}" enctype="multipart/form-data">
    @csrf
@else
<form method="POST" action="{{ url(app()->getLocale() . '/add-event') }}" enctype="multipart/form-data">
    @csrf
@endif

    <input type="text" name="title"
           value="{{ old('title', $event->title ?? '') }}"
           placeholder="عنوان الحدث" required>

    <textarea name="description" placeholder="الوصف">{{ old('description', $event->description ?? '') }}</textarea>

    <input type="datetime-local" name="event_date"
           value="{{ old('event_date') ?? (isset($event) ? date('Y-m-d\TH:i', strtotime($event->event_date)) : '') }}"
           required>

    <input type="file" name="image" accept="image/*">
    <div style="font-size:12px; color:#aaa;">
         في حال عدم اختيار صورة جديدة، سيتم الاحتفاظ بالصورة الحالية
    </div>
    {{-- عرض الصورة الحالية --}}
    @if(isset($event) && $event->image)
      <div style="margin-bottom:15px;">
        <div>الصورة الحالية:</div>
        <img src="/storage/{{ $event->image }}" 
             style="max-width:200px; border-radius:8px; border:1px solid #333;">
      </div>
    @endif

    <input type="text" name="lat"
           value="{{ old('lat', $event->lat ?? request('lat')) }}"
           placeholder="Latitude" required>

    <input type="text" name="lng"
           value="{{ old('lng', $event->lng ?? request('lng')) }}"
           placeholder="Longitude" required>

    <select name="category">
        @foreach([
          'قتل','اعتقال','تهجير','اختطاف','اطلاق نار','مفقود',
          'انفجار','حادث','سطو','فصل','اقتحام','قصف'
        ] as $cat)
            <option value="{{ $cat }}"
                {{ old('category', $event->category ?? '') == $cat ? 'selected' : '' }}>
                {{ $cat }}
            </option>
        @endforeach
    </select>

    <select name="governorate" id="governorate">
        <option value="">اختر المحافظة</option>

        @foreach([
            'دمشق','ريف دمشق','حلب','حمص','حماة','اللاذقية','طرطوس',
            'درعا','السويداء','القنيطرة','دير الزور','الحسكة','الرقة','إدلب'
        ] as $gov)
            <option value="{{ $gov }}"
               {{ old('governorate', $event->governorate ?? '') == $gov ? 'selected' : '' }}>
               {{ $gov }}
            </option>
        @endforeach
    </select>

    <select name="city" id="city">
        <option value="">اختر المدينة</option>

        @if(old('city') || (isset($event) && $event->city))
            <option value="{{ old('city', $event->city ?? '') }}" selected>
                {{ old('city', $event->city ?? '') }}
            </option>
        @endif
    </select>

    <input type="text" name="area"
       value="{{ old('area', $event->area ?? '') }}"
       placeholder="الحي / القرية (اختياري)">

    <div style="margin-top:20px; font-weight:bold;">
        🔍 معلومات التوثيق
    </div>

    <input type="number" name="sources_count"
       value="{{ old('sources_count', $event->sources_count ?? '') }}"
       placeholder="عدد المصادر (مثلاً 2 أو 3)"
       min="0">

    <select name="sources_diverse">
        <option value="0"
            {{ old('sources_diverse', $event->sources_diverse ?? 0) == 0 ? 'selected' : '' }}>
         مصادر متشابهة (من نفس المصدر)
        </option>

        <option value="1"
            {{ old('sources_diverse', $event->sources_diverse ?? 0) == 1 ? 'selected' : '' }}>
         مصادر مختلفة (مستقلة)
        </option>
    </select>

    <input type="text" name="video_url"
       value="{{ old('video_url', $event->video_url ?? '') }}"
       placeholder="رابط فيديو (اختياري)">

    
    <button type="submit">
        {{ isset($event) ? 'تحديث' : 'إضافة' }}
    </button>

</form>

{{-- ========================= --}}
{{-- 🔴 فورم الحذف (مفصول تماماً) --}}
{{-- ========================= --}}
@if(isset($event))
<form method="POST" action="{{ url(app()->getLocale() . '/events/' . $event->id) }}">
    @csrf
    @method('DELETE')

    <button type="submit" class="delete-btn">
        حذف الحدث
    </button>
</form>
@endif

<script src="/js/locations.js"></script>

<script>
document.getElementById('governorate').addEventListener('change', function () {

    const citySelect = document.getElementById('city');
    citySelect.innerHTML = '<option value="">اختر المدينة</option>';

    const selected = this.value;

    if (locations[selected]) {
        locations[selected].forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
    }
});
</script>
</body>
</html>