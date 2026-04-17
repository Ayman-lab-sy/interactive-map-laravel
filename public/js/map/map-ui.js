// ========================================
// ===== FILTER BUTTONS =====
// ========================================

document.querySelectorAll('.filter-item').forEach(item => {
  item.addEventListener('click', function() {

    const type = this.getAttribute('data-type');

    if (activeCategories.includes(type)) {
      activeCategories = activeCategories.filter(c => c !== type);
      this.classList.remove('active');
    } else {
      activeCategories.push(type);
      this.classList.add('active');
    }

    // 📱 إغلاق القائمة على الموبايل
    if (window.innerWidth < 768) {
      const panel = document.getElementById('control-panel');
      panel?.classList.remove('open');
      const overlay = document.getElementById('sidebar-overlay');
      overlay?.classList.remove('active');
    }
    applyFilters();
  });
});

// ========================================
// ===== TOGGLES =====
// ========================================

document.getElementById('toggle-markers').addEventListener('click', function() {
  showMarkers = !showMarkers;
  this.classList.toggle('active');
  window.lastBounds = null;
  if (window.innerWidth < 768) {
    const panel = document.getElementById('control-panel');
    panel?.classList.remove('open');
    const overlay = document.getElementById('sidebar-overlay');
    overlay?.classList.remove('active');
  }
  loadEvents();
});

document.getElementById('toggle-heatmap').addEventListener('click', function() {
  showHeatmap = !showHeatmap;
  this.classList.toggle('active');
  window.lastBounds = null;
  if (window.innerWidth < 768) {
    const panel = document.getElementById('control-panel');
    panel?.classList.remove('open');
    const overlay = document.getElementById('sidebar-overlay');
    overlay?.classList.remove('active');
  }
  loadEvents();
});

// تفعيل افتراضي
document.getElementById('toggle-markers').classList.add('active');
document.getElementById('toggle-heatmap').classList.add('active');

// ========================================
// ===== DATE FILTER SELECT =====
// ========================================

const filterEl = document.getElementById('filter');
const dateRangeBox = document.getElementById('custom-date-range');

if (filterEl) {

  if (window.govParam) {
    filterEl.value = 'all';
  } else {
    filterEl.value = 'today';
  }

  window.loadEvents();

  filterEl.addEventListener('change', function () {

    // 🎯 UI
    if (this.value === 'custom') {
    dateRangeBox.classList.add('active');
  } else {
    dateRangeBox.classList.remove('active');
  }

    // 🔥 DATA
    window.forceReload = true;
    window.lastBounds = null;

    window.loadEvents();

    window.forceReload = false;

    // ✅ 🔥 إغلاق فقط إذا مو custom
    if (this.value !== 'custom' && window.innerWidth < 768) {
      const panel = document.getElementById('control-panel');
      const overlay = document.getElementById('sidebar-overlay');

      panel?.classList.remove('open');
      overlay?.classList.remove('active');
    }
  });

}

// ========================================
// ===== DATE INPUT LISTENERS =====
// ========================================

const dateFrom = document.getElementById('date-from');
const dateTo = document.getElementById('date-to');

function handleDateChange() {

  const filterValue = document.getElementById('filter').value;

  if (filterValue !== 'custom') return;

  const from = dateFrom.value;
  const to = dateTo.value;

  // ❌ لسا ما اكتملت
  if (!from || !to) return;

  // ❌ invalid
  if (!validateDateRange(from, to)) return;

  // 🔥 تحميل البيانات
  window.forceReload = true;
  window.lastBounds = null;

  window.loadEvents();

  window.forceReload = false;

  // ✅ 🔥 هون التسكير (بعد اكتمال التاريخين فقط)
  if (window.innerWidth < 768) {
    const panel = document.getElementById('control-panel');
    const overlay = document.getElementById('sidebar-overlay');

    panel?.classList.remove('open');
    overlay?.classList.remove('active');
  }
}

dateFrom?.addEventListener('change', handleDateChange);
dateTo?.addEventListener('change', handleDateChange);

// ========================================
// ===== MAP CLICK =====
// ========================================

window.map.on('click', function(e) {

  const lat = e.latlng.lat.toFixed(6);
  const lng = e.latlng.lng.toFixed(6);

  if (window.isAdmin) {
    window.location.href = `/${window.locale}/add-event?lat=${lat}&lng=${lng}`;
  } else {
    L.popup()
      .setLatLng([lat, lng])
      .setContent(`
        <div style="font-family:Arial; font-size:13px;">
          <b>📍 الموقع المختار</b><br><br>
          Lat: <span style="color:#00d4ff">${lat}</span><br>
          Lng: <span style="color:#00d4ff">${lng}</span>
        </div>
      `)
      .openOn(window.map);
  }

});

window.map.on('moveend', window.debouncedLoadEvents);

window.map.on('zoomend', function () {

  const zoom = window.map.getZoom();

  // 🔥 إذا المستخدم طلع زوم → نلغي المحافظة
  if (zoom < 9 && window.govParam) {
    window.govParam = null;

    // 🔥 reset الكاش
    window.lastBounds = null;
    window.lastFilter = null;
    window.lastGov = null;
  }

  // 🔥 تحديث heatmap radius (خليه مثل ما هو)
  if (window.heatLayer && window.map.hasLayer(window.heatLayer)) {
    let radius = 35;
    if (zoom < 7) radius = 50;
    else if (zoom < 9) radius = 40;
    else radius = 30;

    window.heatLayer.setOptions({ radius: radius });
  }

  // 🔥 إعادة تحميل
  window.lastBounds = null;
  loadEvents();
});

const legend = document.getElementById('map-legend');
const searchBox = document.getElementById('map-search');
const menuBtn = document.getElementById('menu-toggle');
const zoomControls = document.querySelector('.leaflet-control-container');

function hideUI() {
  legend?.classList.add('ui-hidden');
  searchBox?.classList.add('ui-hidden');
  menuBtn?.classList.add('ui-hidden');
  zoomControls?.classList.add('ui-hidden');
}

function showUI() {
  legend?.classList.remove('ui-hidden');
  searchBox?.classList.remove('ui-hidden');
  menuBtn?.classList.remove('ui-hidden');
  zoomControls?.classList.remove('ui-hidden');
}

window.map.on('popupopen', function () {
  if (window.innerWidth <= 768) {
    document.body.classList.add('popup-focus');
    hideUI();
  }
});

window.map.on('popupclose', function () {
  document.body.classList.remove('popup-focus');

  if (window.innerWidth <= 768) {
    showUI();
  }
});

// ========================================
// ===== SIDEBAR TOGGLE =====
// ========================================

const panel = document.getElementById('control-panel');
const overlay = document.getElementById('sidebar-overlay');
const closeBtn = document.getElementById('close-menu');

// زر الإغلاق
closeBtn?.addEventListener('click', () => {
  panel.classList.remove('open');
  overlay?.classList.remove('active');
});

// زر المينيو (toggle)
menuBtn?.addEventListener('click', (e) => {
  e.stopPropagation();
  panel?.classList.toggle('open');
  overlay?.classList.toggle('active');

  setTimeout(() => {
    window.map.invalidateSize();
  }, 300);
});

// 🔥 الإغلاق بالضغط على الخلفية
overlay?.addEventListener('click', () => {
  panel.classList.remove('open');
  overlay.classList.remove('active');
});

// 🔥 منع الإغلاق عند الضغط داخل القائمة
panel?.addEventListener('click', (e) => {
  e.stopPropagation();
});

// resize (موحد)
window.addEventListener('resize', () => {
  // تحديث حجم الخريطة
  window.map.invalidateSize();
  // إذا رجع المستخدم للكمبيوتر → رجّع كل العناصر
  if (window.innerWidth > 768) {
    legend?.classList.remove('ui-hidden');
    searchBox?.classList.remove('ui-hidden');
    menuBtn?.classList.remove('ui-hidden');
    zoomControls?.classList.remove('ui-hidden');
  }
});

// ========================================
// ===== THEME TOGGLE (داخل القائمة)
// ========================================

let isDark = false;

const themeBtn = document.getElementById('toggle-theme');

themeBtn?.addEventListener('click', () => {

  if (isDark) {
    window.map.removeLayer(darkMap);
    lightMap.addTo(window.map);

    themeBtn.innerText = "🌙 الوضع الليلي";
  } else {
    window.map.removeLayer(lightMap);
    darkMap.addTo(window.map);

    themeBtn.innerText = "☀️ الوضع النهاري";
  }

  isDark = !isDark;

  themeBtn.classList.toggle('active');
  // 📱 إغلاق القائمة على الموبايل
  if (window.innerWidth < 768) {
    const panel = document.getElementById('control-panel');
    panel?.classList.remove('open');
    const overlay = document.getElementById('sidebar-overlay');
    overlay?.classList.remove('active');
  }
});

// ========================================
// ===== LIVE UPDATE (Auto Refresh)
// ========================================

setInterval(() => {

  // ❌ لا نحدث إذا المستخدم يفلتر حالياً
  if (window.isFiltering) return;
  if (window.isOpeningEvent) return; // 🔥 إضافة

  // ❌ 🔥 لا نحدث إذا عم نفتح حدث من رابط
  if (window.isOpeningEvent) return;

  const isMobile = window.innerWidth < 768;
  const panel = document.getElementById('control-panel');

  // 🔥 فقط على الموبايل
  if (isMobile && panel && panel.classList.contains('open')) return;

  // 🔥 تحديث ذكي
  window.loadEvents();

  // ✨ تأثير بصري (خليه)
  document.getElementById('map-container')?.classList.remove('map-loaded');

  setTimeout(() => {
    document.getElementById('map-container')?.classList.add('map-loaded');
  }, 100);

}, 15000);

// ========================================
// ===== MAP LEGEND
// ========================================

function updateLegend() {

  const values = Object.values(window.govCounts);
  if (values.length === 0) return;

  const max = Math.max(...values, 1);

  const step1 = Math.round(max * 0.25);
  const step2 = Math.round(max * 0.5);
  const step3 = Math.round(max * 0.75);

  const legendHTML = `
    <div class="legend-section">
      <div class="legend-subtitle">الكثافة</div>

      <div class="legend-gradient"></div>

      <div class="legend-labels">
        <span>${max}+</span>
        <span>${step3}</span>
        <span>${step2}</span>
        <span>${step1}</span>
        <span>0</span>
      </div>

      <div class="legend-desc">
        عدد الأحداث ضمن المنطقة
      </div>
    </div>

    <div class="legend-divider"></div>

    <div class="legend-section">
      <div class="legend-subtitle">موثوقية الحدث</div>

      <div class="legend-confidence">
        <span class="conf high">🟢 عالي</span>
        <span class="conf medium">🟡 متوسط</span>
        <span class="conf low">🔴 ضعيف</span>
      </div>
    </div>
  `;

  document.getElementById('map-legend').innerHTML = legendHTML;
}

// ========================================
// ===== MARKERS STATES
// ========================================
document.getElementById('toggle-view-mode').addEventListener('click', function () {

  // 🔁 تبديل الوضع
  window.viewMode =
    window.viewMode === 'governorates' ? 'points' : 'governorates';

  // 🔥 reset بسيط فقط (بدون تخبيص)
  window.lastBounds = null;

  // ❌ مهم: لا نلمس الفلاتر ولا govParam

  // 🔥 تنظيف layers
  window.markersCluster.clearLayers();
  window.governoratesLayer.clearLayers();

  if (window.heatLayer && window.map.hasLayer(window.heatLayer)) {
    window.map.removeLayer(window.heatLayer);
  }

  // 🔥 تحديث الزر
  if (window.viewMode === 'governorates') {
    this.classList.add('active');
  } else {
    this.classList.remove('active');
  }
  if (window.innerWidth < 768) {
    const panel = document.getElementById('control-panel');
    panel?.classList.remove('open');
    const overlay = document.getElementById('sidebar-overlay');
    overlay?.classList.remove('active');
  }
  // 🚀 إعادة تحميل
  loadEvents();
});