// ========================================
// ===== FILTER STATE =====
// ========================================

let activeCategories = [];
let showHeatmap = true;
let showMarkers = true;
window.isFiltering = false;
let moveTimeout = null;
let isFirstLoad = true;
window.viewMode = 'points';

// ========================================
// ===== APPLY FILTERS =====
// ========================================

function applyFilters(bounds = null) {
  if (window.isFiltering) return;
  window.isFiltering = true;

  // 🔥 منع تنفيذ الفلاتر بوضع المحافظات الكامل
  if ((window.smartView || window.viewMode) !== 'points') {
    window.isFiltering = false;
    return;
  }

  let filtered = [...window.allEvents];

  const emptyEl = document.getElementById('map-empty');

  if (filtered.length === 0) {
    emptyEl?.classList.add('active');
  } else {
    emptyEl?.classList.remove('active');
  }

  // فلتر النوع
  if (activeCategories.length > 0) {
    filtered = filtered.filter(event => {
      if (!event.category) return false;

      const eventCategory = event.category.trim();

      return activeCategories.some(type =>
        eventCategory.includes(type)
      );
    });
  }


  let heatData = [];
  // 🔥 تحديد المود الفعلي
  const currentMode = window.smartView || window.viewMode;
  // ==========================
  // POINTS MODE
  // ==========================
  if (currentMode === 'points') {
    heatData = filtered.slice(0, 500).map(event => [
      event.lat,
      event.lng,
      1
    ]);
  }
  // ==========================
  // GOVERNORATES MODE
  // ==========================
  else if (currentMode === 'governorates') {
    const govCounts = {};
    filtered.forEach(e => {
      if (!e.governorate) return;
      if (!govCounts[e.governorate]) {
        govCounts[e.governorate] = {
          count: 0,
          lat: e.lat,
          lng: e.lng
        };
      }
      govCounts[e.governorate].count += 1;
    });
    heatData = Object.values(govCounts).map(gov => [
      gov.lat,
      gov.lng,
      gov.count
    ]);
  }
  // 🔥 تطبيق heatmap
  if (window.heatLayer && heatData.length > 0) {
    if (!window.map.hasLayer(window.heatLayer)) {
      window.heatLayer.addTo(window.map);
    }
    if (typeof window.heatLayer.setLatLngs === 'function') {
      window.heatLayer.setLatLngs(heatData);
    }
  }

  const counts = {
    'قتل': 0,'اعتقال': 0,'تهجير': 0,'انفجار': 0,
    'حادث': 0,'اختطاف': 0,'اطلاق نار': 0,'مفقود': 0,
    'سطو': 0,'فصل': 0,'اقتحام': 0,'قصف': 0
  };

  filtered.forEach(event => {
    if (!event.category) return;

    Object.keys(counts).forEach(type => {
      if (event.category.includes(type)) {
        counts[type]++;
      }
    });
  });

  document.querySelectorAll('.filter-item').forEach(item => {

    const type = item.getAttribute('data-type');
    const count = counts[type] || 0;

    const old = item.querySelector('.count');
    if (old) old.remove();

    const span = document.createElement('span');
    span.className = 'count';
    span.textContent = `(${count})`;
    span.style.marginRight = 'auto';
    span.style.opacity = '0.7';

    item.appendChild(span);
  });

  // 🔥 HEATMAP CONTROL (FIXED)
  if (!window.heatLayer) return;
  // 🟢 تشغيل
  if (showHeatmap && heatData.length > 0) {
    if (!window.map.hasLayer(window.heatLayer)) {
      window.heatLayer.addTo(window.map);
    }
    if (typeof window.heatLayer.setLatLngs === 'function') {
      window.heatLayer.setLatLngs(heatData);
    }
  }
  // 🔴 إيقاف
  else {
    if (window.map.hasLayer(window.heatLayer)) {
      window.map.removeLayer(window.heatLayer);
    }
    // 🔥 يمنع الخطأ تبع getSize
    if (typeof window.heatLayer.setLatLngs === 'function') {
     window.heatLayer.setLatLngs([]);
    }
  }
  window.isFiltering = false;
}