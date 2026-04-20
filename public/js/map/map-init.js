window.lastBounds = null;
window.lastGov = null;
window.lastFilter = null;
window.lastFrom = null;
window.lastTo = null;
window.popupAdjusted = false;

// ========================================
// ===== DATA (STATE) =====
// ========================================

window.allEvents = [];
window.allMarkersMap = {};
window.govCounts = {};
window.lastGovCounts = {};

const govMap = {
  "Homs": "حمص",
  "Damascus": "دمشق",
  "Aleppo": "حلب",
  "Hama": "حماة",
  "Lattakia": "اللاذقية",
  "Tartous": "طرطوس",
  "Dar'a": "درعا",
  "Idleb": "إدلب",
  "Ar-Raqqa": "الرقة",
  "Deir-ez-Zor": "دير الزور",
  "Al-Hasakeh": "الحسكة",
  "As-Sweida": "السويداء",
  "Quneitra": "القنيطرة",
  "Rural Damascus": "ريف دمشق"
};

// ========================================
// ===== URL PARAMS =====
// ========================================

const params = new URLSearchParams(window.location.search);
const selectedLat = params.get('lat');
const selectedLng = params.get('lng');
window.govParam = params.get('gov');
const rangeParam = params.get('range');
const fromParam = params.get('from');
const toParam = params.get('to');

// 🔥 تطبيق الفلتر تلقائياً
if (rangeParam) {
  const filterEl = document.getElementById('filter');
  if (filterEl) {
    filterEl.value = rangeParam;
  }
}

if (fromParam && toParam) {
  const filterEl = document.getElementById('filter');
  if (filterEl) {
    filterEl.value = 'custom';
  }

  const fromInput = document.getElementById('date-from');
  const toInput = document.getElementById('date-to');

  if (fromInput) fromInput.value = fromParam;
  if (toInput) toInput.value = toParam;
}

// ========================================
// ===== MAP INIT (تهيئة الخريطة) =====
// ========================================
window.targetEventId = params.get('event');
window.eventOpened = false;
window.isOpeningEvent = false;
window.map = L.map('map', {
  preferCanvas: true
});

if (window.targetEventId) {

  fetch(`/api/events/${window.targetEventId}`)
    .then(res => res.json())
    .then(data => {

      if (!data || !data.lat || !data.lng) return;

      const latlng = [data.lat, data.lng];

      // 🎯 روح مباشرة للموقع
      window.map.setView(latlng, 10);

      // خزّن الحدث مؤقتاً
      window.directEvent = data;

      // بعد تحميل الماركرز رح نفتحو
    });
} 

window.geoLayer = null;

fetch('/data/syria.geojson')
  .then(res => res.json())
  .then(data => {

    window.geoLayer = L.geoJSON(data, {
      style: function(feature) {

        const rawName =
          feature.properties.NAME_ARA ||
          feature.properties.name ||
          feature.properties.NAME_1;
        const name = govMap[rawName] || rawName;
        const count = window.govCounts[name] || 0;
        const color = window.getColorByCount(count);

        return {
          color: '#000',
          weight: 1,
          fillColor: color,
          fillOpacity: 0.5
        };
      }
    }).addTo(window.map);

  });


window.map.fitBounds([
  [32.5, 35.5], // جنوب غرب سوريا
  [37.5, 42.0]  // شمال شرق سوريا
]);

window.addEventListener('load', () => {
  setTimeout(() => {
    window.map.invalidateSize();
  }, 300);
});

const lightMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenStreetMap'
});

const darkMap = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
  attribution: '&copy; OpenStreetMap'
});

lightMap.addTo(window.map);