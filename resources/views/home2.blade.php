@extends('layouts.main')

@section('title', 
'منظمة العلويين والأقليات السورية للعدالة والسلام | دعم حقوق الإنسان وتمثيل الأقليات في أوروبا'
)

@section('meta')

<meta name="description" content="منظمة مستقلة مسجلة في النمسا تدافع عن حقوق الأقليات السورية، تقدم الدعم القانوني، التمثيل السياسي، توثيق الانتهاكات والمساعدات الإنسانية.">

<meta property="og:title" content="منظمة العلويين والأقليات السورية للعدالة والسلام">
<meta property="og:description" content="دعم حقوق الإنسان، تمثيل سياسي، دعم قانوني، توثيق الانتهاكات للأقليات السورية في أوروبا.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">
<meta property="og:image" content="https://www.thealawites.com/assets/logo.png">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="منظمة العلويين والأقليات السورية للعدالة والسلام">
<meta name="twitter:description" content="منظمة مستقلة تدافع عن حقوق الأقليات السورية في أوروبا.">
<meta name="twitter:image" content="https://www.thealawites.com/assets/logo.png">

@endsection

@section('content')

  <header class="hero">
    <img src="/assets/logo.png" alt="شعار المنظمة" class="logo">
    <h1>منظمة العلويين والأقليات السورية للعدالة والسلام</h1>
    <p>دعم حقوق الإنسان، تمثيل سياسي، دعم قانوني، توثيق الانتهاكات</p>
    <div class="hero-buttons">
      <a href="{{ route('donate', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">قدّم دعمك</a>
      <a href="{{ url(app()->getLocale() . '/join') }}" class="btn btn-outline"> انضم للمنظمة</a>
      <a href="{{ url(app()->getLocale().'/documentation-new') }}" class="btn btn-outline">توثيق حالة</a>
      <a href="{{ route('home', ['locale' => 'en']) }}" class="btn">
        English
      </a>
    </div>
  </header>
  
  <div class="soft-launch-bar">
    <div class="soft-launch-text">
      هذا الموقع في مرحلة تشغيل تجريبي، نعمل على تحسين المحتوى والخدمات بشكل مستمر. نشكركم على تفهمكم.
    </div>
  </div>

  <section class="section documentation-cta" style="background:#f8fafc; text-align:center; padding:40px 20px;">

    <h2 style="font-size:26px; margin-bottom:15px;">
      هل تتعرض لتهديد أو خطر مباشر؟
    </h2>

    <p style="font-size:16px; color:#444; max-width:700px; margin:0 auto 20px;">
      يمكنك إرسال حالتك بشكل آمن وسري، وسيتم توثيقها ومتابعتها عبر قنوات حقوقية دولية.
    </p>

    <div style="margin-bottom:20px; color:#059669; font-size:14px;">
      ✔ جميع المعلومات سرية بالكامل  
      ✔ يمكنك استخدام اسم مستعار  
      ✔ لن يتم مشاركة بياناتك بدون موافقتك  
    </div>

    <a href="{{ url(app()->getLocale().'/documentation-new') }}" 
       class="btn"
       style="font-size:18px; padding:12px 25px;">
      📩 إرسال حالتي الآن
    </a>

  </section>

  <section class="section goals">
    <h2>أهدافنا</h2>
    <div class="grid">
      <div class="card">
        <img src="/assets/icons/awareness.svg" alt="التوعية">
        <h3>التوعية</h3>
        <p>تعزيز وعي المجتمع بالتحديات التي تواجه الأقليات.</p>
      </div>
      <div class="card">
        <img src="/assets/icons/politics.svg" alt="التمثيل السياسي">
        <h3>التمثيل السياسي</h3>
        <p>المطالبة بحق تقرير المصير والدفاع عن الحقوق.</p>
      </div>
      <div class="card">
        <img src="/assets/icons/legal.svg" alt="الدعم القانوني">
        <h3>الدعم القانوني</h3>
        <p>تقديم الدعم القانوني وتمثيل الأقليات دولياً.</p>
      </div>
      <div class="card">
        <img src="/assets/icons/aid.svg" alt="المساعدات">
        <h3>المساعدات الإنسانية</h3>
        <p>تقديم المساعدات خلال الأزمات (غذاء، صحة، تعليم...)</p>
      </div>
      <div class="card">
        <img src="/assets/icons/training.svg" alt="التعليم والتدريب">
        <h3>التعليم والتدريب</h3>
        <p>تنفيذ برامج تدريب لتمكين الأقليات من الدفاع عن حقوقها.</p>
      </div>
      <div class="card">
        <img src="/assets/icons/docs.svg" alt="التوثيق">
        <h3>التوثيق</h3>
        <p>توثيق الانتهاكات والجرائم بحق الأقليات وتقديمها للجهات المختصة.</p>
      </div>
    </div>
  </section>
  
  <section class="map-preview-section">

    <h2 style="text-align:center;">🗺️ خريطة الأحداث</h2>

    <p style="text-align:center; max-width:700px; margin:10px auto 30px; color:#555;">
      استكشف الأحداث الموثقة بشكل تفاعلي حسب المحافظة والتاريخ، واطّلع على التفاصيل الدقيقة لكل حالة.
    </p>

    <!-- 🔥 هذا هو الحل -->
    <div class="map-preview-content">

      <div class="map-preview-box">
        <div class="map-overlay-text">اضغط لاستكشاف الخريطة</div>
        <div id="mini-map"></div>
      </div>

      <div class="map-text">

        <div class="map-stats">

          <div class="stat-box">
           <span id="stat-total">--</span>
           <small>حدث موثّق</small>
         </div>

         <div class="stat-box">
           <span>14</span>
           <small>محافظة</small>
         </div>

        </div>

        <div style="text-align:center; margin-top:20px;">
          <a href="{{ route('map', ['locale' => app()->getLocale()]) }}" class="btn">
            استعرض الخريطة الكاملة →
          </a>
        </div>

      </div>

    </div>

  </section>

  <section class="section news-section" id="news">
    <h2>أخبارنا</h2>
    <p style="text-align:center; margin-top:-20px; color:#555;">
      تابع آخر أنشطة وبيانات منظمتنا
    </p>

    <div class="grid">
      @forelse($latestNews ?? [] as $news)
        <div class="card">
          @if($news->image)
            <img src="{{ asset('storage/'.$news->image) }}" alt="{{ $news->title }}">
          @endif

          <h3>{{ $news->title }}</h3>
          <small>{{ $news->date }}</small>
          <p>{{ $news->summary }}</p>

          <div style="text-align:center; margin-top:15px;">
            <a class="btn btn-outline"
               href="{{ url(app()->getLocale().'/news-new/'.$news->slug) }}">
             اقرأ المزيد
           </a>
          </div>
        </div>
      @empty
       <p style="text-align:center;">لا توجد أخبار حاليًا</p>
      @endforelse
    </div>

    <div style="text-align:center; margin-top:25px;">
      <a href="{{ url(app()->getLocale().'/news-new') }}" class="btn btn-outline">
        عرض كل الأخبار
      </a>
    </div>
  </section>

  <section class="section about">
    <h2>من نحن؟</h2>
    <p>
      منظمة مستقلة غير ربحية مسجّلة في النمسا، أُسست لحماية وتمكين الأقليات السورية، وعلى رأسها الأقلية العلوية. نعمل على الدفاع عن الحقوق، التمثيل السياسي، الدعم القانوني، والتوثيق الإنساني. نعمل من النمسا لدعم حقوق الأقليات السورية في أوروبا والدفاع عن قضاياهم أمام الجهات الدولية
    </p>

    <div style="margin-top: 25px;">
      <a href="{{ route('about', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">تعرف علينا أكثر</a>
    </div>
  </section>

@endsection

@section('home-scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

  const section = document.querySelector('.map-preview-section');
  const miniMapEl = document.getElementById('mini-map');
  if (!section || !miniMapEl) return;

  // 1️⃣ أظهر السكشن أولاً
  requestAnimationFrame(() => {
    section.classList.add('visible');

    // 2️⃣ بعد ما يظهر فعلياً، ابني الخريطة
    setTimeout(() => {

      const miniMap = L.map('mini-map', {
        zoomControl: false,
        attributionControl: false,
        dragging: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        keyboard: false,
        tap: false
      }).setView([34.8, 38], 6);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 10
      }).addTo(miniMap);

      miniMap.on('click', function () {
        window.location.href = "{{ route('map', ['locale' => app()->getLocale()]) }}";
      });

      // تحميل النقاط
      fetch('/api/events')
        .then(res => res.json())
        .then(response => {

          const events = response.data;
          const total = response.meta?.total || 0;

          const statEl = document.getElementById('stat-total');
          if (statEl) statEl.innerText = total;

          if (!events || events.length === 0) return;

          events.slice(0, 20).forEach(event => {
            if (!event.lat || !event.lng) return;

            L.circleMarker([event.lat, event.lng], {
              radius: 6,
              color: 'transparent',
              fillColor: '#ef4444',
              fillOpacity: 0.4
            }).addTo(miniMap);

            L.circleMarker([event.lat, event.lng], {
              radius: 12,
              color: 'transparent',
              fillColor: '#ef4444',
              fillOpacity: 0.15
            }).addTo(miniMap);
          });
        });

    }, 300); // 👈 مهم
  });

});
</script>
@endsection