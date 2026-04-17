@extends('layouts.main', [
    'pageClass' => 'map-page',
    'hideFooter' => true
])

@section('title', 'خريطة الأحداث')

@section('content')


<link rel="stylesheet" href="/css/map.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />



<div id="layout">
  <div id="sidebar-overlay"></div>

  <div id="map-container">

    <button id="menu-toggle">☰</button>

    <div id="map-search">
      <input 
        type="text" 
        id="search-input" 
        placeholder="🔎 ابحث عن مدينة أو قرية..."
        autocomplete="off"
      />

      <div id="search-results"></div>
    </div>

    <div id="map"></div>

    <div id="map-loader">
      <div class="loader-spinner"></div>
    </div>

    <div id="map-empty">
       لا توجد بيانات ضمن هذا النطاق
    </div>

    <div id="map-legend"></div>

  </div>

  <div id="control-panel">

    <button id="close-menu">✕</button>
    
    <div class="section">
      <div class="legend-title">العرض</div>

      <div class="toggle-item" id="toggle-markers">
        🟢 النقاط
      </div>

      <div class="toggle-item" id="toggle-heatmap">
        🔥 الكثافة
      </div>

      <div class="toggle-item" id="toggle-view-mode">
        🧭 وضع المحافظات
      </div>
    </div>

    <div class="section">
      <div class="panel-title">الفلاتر</div>

      <!-- فلتر الزمن -->
      <select id="filter" class="filter-select">
        <option value="today">اليوم</option>
        <option value="week">هذا الأسبوع</option>
        <option value="month">هذا الشهر</option>
        <option value="custom">تحديد فترة</option>
          @if(auth()->check() && auth()->user()->role_id === 1)
            <option value="all">كل الأحداث</option>
          @endif
      </select>

      <div id="custom-date-range" class="date-range-box">

        <label>من</label>
        <input type="date" id="date-from" class="filter-select" />

        <label>إلى</label>
        <input type="date" id="date-to" class="filter-select" />

        <div id="date-error" class="date-error">
           الحد الأقصى للفترة هو 7 أيام فقط
        </div>

      </div>

      <div id="results-info"></div>
    </div>


    <div class="section">

      <div class="legend-title">نوع الحدث</div>

      <div class="filter-item" data-type="قتل">
        <img src="/icons/kill.png">
         قتل
      </div>

      <div class="filter-item" data-type="اعتقال">
        <img src="/icons/arrest.png">
         اعتقال
      </div>

      <div class="filter-item" data-type="تهجير">
        <img src="/icons/displacement.png">
       تهجير
      </div>

      <div class="filter-item" data-type="انفجار">
        <img src="/icons/explosion.png">
         انفجار
      </div>

      <div class="filter-item" data-type="حادث">
        <img src="/icons/accident.png">
         حادث
      </div>

      <div class="filter-item" data-type="اختطاف">
        <img src="/icons/abduction.png">
         اختطاف
      </div>

      <div class="filter-item" data-type="اطلاق نار">
        <img src="/icons/shooting.png">
         اطلاق نار
      </div>

      <div class="filter-item" data-type="مفقود">
        <img src="/icons/missing.png">
           مفقود
      </div>

      <div class="filter-item" data-type="فصل">
        <img src="/icons/termination.png">
           فصل من العمل
      </div>

      <div class="filter-item" data-type="سطو">
        <img src="/icons/theft.png">
           سطو/سرقة
      </div>

      <div class="filter-item" data-type="اقتحام">
        <img src="/icons/raid.png">
         اقتحام
      </div>

      <div class="filter-item" data-type="قصف">
        <img src="/icons/bombing.png">
         قصف/تحليق
      </div>
    </div>

    <div class="section">

      <div class="legend-title">الخريطة</div>

      <div class="toggle-item" id="toggle-theme">
        🌙 الوضع الليلي
      </div>
    </div>

  </div>
</div>  

<script>
  window.isAdmin = {{ auth()->check() && auth()->user()->role_id === 1 ? 'true' : 'false' }};
  window.locale = "{{ app()->getLocale() }}";
  window.csrf = "{{ csrf_token() }}";
</script>

@endsection

@section('map-scripts')

<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

<script src="/js/locations.js"></script>
<script src="/js/map/map-init.js"></script>
<script src="/js/map/map-layers.js"></script>
<script src="/js/map/map-markers.js"></script>
<script src="/js/map/map-data.js"></script>
<script src="/js/map/map-render.js"></script>
<script src="/js/map/map-filters.js"></script>
<script src="/js/map/map-ui.js"></script>
<script src="/js/map-search.js"></script>

@endsection