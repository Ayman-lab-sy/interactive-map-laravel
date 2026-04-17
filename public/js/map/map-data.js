// ========================================
// ===== COORDS =====
// ========================================
const governorateCoords = {
  "دمشق": [33.5138, 36.2765],
  "ريف دمشق": [33.6, 36.4],
  "حلب": [36.2021, 37.1343],
  "حمص": [34.7324, 36.7137],
  "حماة": [35.1318, 36.7578],
  "اللاذقية": [35.5317, 35.7900],
  "طرطوس": [34.8890, 35.8866],
  "درعا": [32.6189, 36.1021],
  "السويداء": [32.7080, 36.5667],
  "القنيطرة": [33.1256, 35.8246],
  "دير الزور": [35.3333, 40.1500],
  "الحسكة": [36.5, 40.75],
  "الرقة": [35.95, 39.0167],
  "إدلب": [35.9306, 36.6339]
};


// ========================================
// ===== FETCH DATA =====
// ========================================

window.loadEvents = function() {
  if (!window.map) return;
  if (!window.markersCluster) return;
  if (!window.governoratesLayer) return;
  if (window.isOpeningEvent) return;
  if (document.hidden) return;
  isFirstLoad = false;

  const bounds = window.map.getBounds();
  // 🔥 مقارنة ذكية (تقريبية)
  function isClose(a, b, tolerance = 0.05) {
    return Math.abs(a - b) < tolerance;
  }

  const filterValue = document.getElementById('filter').value;
  if (window.lastBounds) {
    const sameBounds =
      isClose(bounds.getNorth(), window.lastBounds.getNorth()) &&
      isClose(bounds.getSouth(), window.lastBounds.getSouth()) &&
      isClose(bounds.getEast(), window.lastBounds.getEast()) &&
      isClose(bounds.getWest(), window.lastBounds.getWest());

    let sameFilter = (
      filterValue === window.lastFilter &&
      (window.govParam || null) === (window.lastGov || null)
    );

    // 🔥 إضافة مقارنة التاريخ
    if (filterValue === 'custom') {
      const from = document.getElementById('date-from').value;
      const to = document.getElementById('date-to').value;

      sameFilter = sameFilter &&
        from === window.lastFrom &&
        to === window.lastTo;
    }

    if (sameBounds && sameFilter) return;
  }

  // خزّن الحدود الجديدة
  window.lastBounds = bounds;
  window.lastFilter = filterValue;
  window.lastGov = window.govParam;
  if (filterValue === 'custom') {
    window.lastFrom = document.getElementById('date-from').value;
    window.lastTo = document.getElementById('date-to').value;
  }
  
  const zoom = window.map.getZoom();
  let url;
  if (window.targetEventId) {
    // ✅ وضع المشاركة (Event Mode)
    url = `/api/events/${window.targetEventId}`;
  } else {
    // ✅ الوضع الطبيعي
    url = `/api/events?range=${filterValue}`;
  }

  if (filterValue === 'custom') {
    const from = document.getElementById('date-from').value;
    const to = document.getElementById('date-to').value;
    if (!validateDateRange(from, to)) return;

    url = `/api/events?from=${from}&to=${to}`;
  }

  if (window.govParam) {
    url += `&governorate=${window.govParam}`;
  }
  url += `&north=${bounds.getNorth()}`
    + `&south=${bounds.getSouth()}`
    + `&east=${bounds.getEast()}`
    + `&west=${bounds.getWest()}`;

  document.getElementById('map-container')?.classList.remove('map-loaded');
  showLoader();

  fetch(url)
    .then(res => res.json())
    .then(response => {
      let events;
      if (window.targetEventId) {
        events = [response]; // نحول الحدث لمصفوفة
      } else {
        events = response.data;
      }
      window.realTotal = response.meta?.real_total || events.length;
      const total = response.meta?.total || 0;
      const count = response.meta?.count || total;
      const globalTotal = response.meta?.global_total || total;
      const infoEl = document.getElementById('results-info');
      let filterText = '';

      if (filterValue === 'today') {
        filterText = '📅 اليوم';
      } else if (filterValue === 'week') {
        filterText = '📅 هذا الأسبوع';
      } else if (filterValue === 'month') {
        filterText = '📅 هذا الشهر';
      } else if (filterValue === 'custom') {

        const from = document.getElementById('date-from').value;
        const to = document.getElementById('date-to').value;

        if (from && to) {
          filterText = `📅 من ${from} إلى ${to}`;
        } else {
            filterText = '📅 تحديد فترة';
        }
      }

      if (infoEl) {
        infoEl.innerHTML = `
          📊 يتم عرض ${count} من أصل ${total} حدث<br>
          ${filterText}
        `;

        if (total > count) {
          infoEl.innerHTML += `<br>🔎 توجد نتائج إضافية خارج النطاق الحالي`;
        }
      }

      const emptyEl = document.getElementById('map-empty');
      
      // ========================================
      // ===== EMPTY STATE (FIXED) =====
      // ========================================

      if (window.targetEventId) {
        // 🔥 وضع مشاركة → لا تظهر أي رسالة
        emptyEl?.classList.remove('active');
      }
      else {
        if (total === 0 && globalTotal > 0) {
          emptyEl?.classList.add('active');
          emptyEl.innerText = "لا توجد أحداث في هذه المنطقة، حاول تحريك الخريطة";
        }
        else if (total === 0) {
          emptyEl?.classList.add('active');
          emptyEl.innerText = "لا توجد أحداث ضمن هذه الفترة";
        }
        else {
          emptyEl?.classList.remove('active');
        }
      }

      // 🔥 zoom على المحافظة إذا موجودة
      if (window.govParam && events.length > 0 && !window.zoomed) {
        const avgLat = events.reduce((sum, e) => sum + Number(e.lat), 0) / events.length;
        const avgLng = events.reduce((sum, e) => sum + Number(e.lng), 0) / events.length;
        window.map.setView([avgLat, avgLng], 8);
        window.zoomed = true;
      }

      window.allEvents = events;

      // ==========================
      // 🔥 HEATMAP (المكان الوحيد)
      // ==========================
      const currentMode = window.viewMode;
      let heatData = [];
      // ==========================
      // POINTS
      // ==========================
      if (currentMode === 'points') {
        heatData = events.slice(0, 500).map(e => [
          e.lat,
          e.lng,
          1
        ]);
      }
      // ==========================
      // GOVERNORATES
      // ==========================
      else {
        const govCounts = {};
        events.forEach(e => {
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
        heatData = Object.values(govCounts).map(g => [
          g.lat,
          g.lng,
          g.count
        ]);
      }
      // ==========================
      // APPLY
      // ==========================
      if (showHeatmap && heatData.length > 0) {
        if (!window.map.hasLayer(window.heatLayer)) {
          window.heatLayer.addTo(window.map);
        }
        window.heatLayer.setLatLngs(heatData);
      } else {
        if (window.map.hasLayer(window.heatLayer)) {
          window.map.removeLayer(window.heatLayer);
        }
      }

      // 🔥 1. حساب البيانات أولاً
      window.govCounts = {};
      events.forEach(e => {
        if (!e.governorate) return;
        if (!window.govCounts[e.governorate]) {
          window.govCounts[e.governorate] = 0;
        }
        window.govCounts[e.governorate] += (e.count || 1);
      });

      updateLegend();

      // 🔥 2. بعدين التلوين
      function isSameCounts(a, b) {
        const keys = new Set([...Object.keys(a), ...Object.keys(b)]);
        for (let k of keys) {
          if ((a[k] || 0) !== (b[k] || 0)) return false;
        }
        return true;
      }

      const sameData = isSameCounts(window.govCounts, window.lastGovCounts);

      if (window.geoLayer && !sameData) {
        geoLayer.setStyle(function(feature) {
          const rawName =
            feature.properties.NAME_ARA ||
            feature.properties.name ||
            feature.properties.NAME_1;
          const name = govMap[rawName] || rawName;
          const count = window.govCounts[name] || window.govCounts[rawName] || 0;
          const color = window.getColorByCount(count);
          return {
            color: '#000',
            weight: 1,
            fillColor: color,
            fillOpacity: 0.35
          };
        });
        // ✅ نحفظ آخر حالة
        window.lastGovCounts = { ...window.govCounts };
      }

      let modeToRender = window.viewMode || 'points';
      
      // ==========================
      // RENDER LOGIC (FIXED)
      // ==========================
      const dataToRender = window.filteredEvents || events;
      // 🔥 تنظيف الطبقات
      window.markersCluster.clearLayers();
      window.governoratesLayer.clearLayers();

      // ==========================
      // GOVERNORATES
      // ==========================
      if (modeToRender === 'governorates') {
        // ❌ لا تحذف layer
        window.markersCluster.clearLayers();
        if (!window.map.hasLayer(window.governoratesLayer)) {
          window.map.addLayer(window.governoratesLayer);
        }
        window.renderGovernorates(dataToRender);
      }

      // ==========================
      // POINTS
      // ==========================
      else {
        window.map.removeLayer(window.governoratesLayer);
        if (!window.map.hasLayer(window.markersCluster)) {
          window.map.addLayer(window.markersCluster);
        }
        window.renderPoints(dataToRender);
      }

      if (window.targetEventId && !window.eventOpened) {
        setTimeout(() => {
          const ev = window.allMarkersMap?.[String(window.targetEventId)];
          if (ev && ev.marker && window.map.hasLayer(window.markersCluster)) {
            window.isOpeningEvent = true;
            const latlng = ev.marker.getLatLng();
            window.markersCluster.zoomToShowLayer(ev.marker, () => {
              window.map.setView(latlng, 10);
              ev.marker.openPopup();

              setTimeout(() => {
                window.isOpeningEvent = false;
              }, 1500);
            });
            window.eventOpened = true;
            window.targetEventId = null;
          }
        }, 300); // 🔥 هذا هو الحل الحقيقي
      }
      document.getElementById('map-container')?.classList.add('map-loaded');
    })
    .finally(() => {
      setTimeout(() => {
        hideLoader();
      }, 300);
    });
}

// ========================================
// ===== VALIDATION RANGE =====
// ========================================

function validateDateRange(from, to) {

  const errorEl = document.getElementById('date-error');

  if (!from || !to) {
    errorEl.style.display = 'none';
    return false;
  }

  const start = new Date(from);
  const end = new Date(to);

  const diffDays = (end - start) / (1000 * 60 * 60 * 24);

  // ❌ تاريخ غلط
  if (diffDays < 0) {
    errorEl.innerText = "التاريخ غير صحيح";
    errorEl.style.display = 'block';
    return false;
  }

  // ❌ أكثر من 7 أيام
  if (diffDays > 7) {
    errorEl.innerText = "الحد الأقصى 7 أيام فقط";
    errorEl.style.display = 'block';
    return false;
  }

  // ✅ تمام
  errorEl.style.display = 'none';
  return true;
}

// ========================================
// ===== lOADER SPINNER =====
// ========================================

function showLoader() {
  document.getElementById('map-loader')?.classList.add('active');
}

function hideLoader() {
  document.getElementById('map-loader')?.classList.remove('active');
}

// ========================================
// ===== HELPER FUNCTIONS =====
// ========================================

function debounce(fn, delay) {
  let timeout;

  return function(...args) {
    if (timeout) {
      clearTimeout(timeout);
    }

    timeout = setTimeout(() => {
      fn.apply(this, args);
    }, delay);
  };
}

window.debouncedLoadEvents = debounce(loadEvents, 1200);