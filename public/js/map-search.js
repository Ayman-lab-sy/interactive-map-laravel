// ========================================
// ===== MAP SEARCH (STEP 1)
// ========================================
const searchInput = document.getElementById('search-input');
searchInput?.addEventListener('keypress', function (e) {
  if (e.key !== 'Enter') return;
  const query = this.value.trim();
  if (!query) return;
  fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=sy&accept-language=ar&q=${encodeURIComponent(query)}`)
    .then(res => res.json())
    .then(data => {
      if (!data || data.length === 0) {
        alert("لم يتم العثور على الموقع");
        return;
      }
      const place = data[0];
      const lat = parseFloat(place.lat);
      const lon = parseFloat(place.lon);
      // 🎯 Zoom ذكي حسب حجم المنطقة
      if (place.boundingbox) {
        const south = parseFloat(place.boundingbox[0]);
        const north = parseFloat(place.boundingbox[1]);
        const west  = parseFloat(place.boundingbox[2]);
        const east  = parseFloat(place.boundingbox[3]);
        const bounds = [
          [south, west],
          [north, east]
        ];
        window.map.fitBounds(bounds);
        // 📍 حذف الماركر القديم إذا موجود
        if (window.searchMarker) {
            window.map.removeLayer(window.searchMarker);
        }
        // 📍 إضافة ماركر جديد
        window.searchMarker = L.marker([lat, lon]).addTo(window.map)
            .bindPopup(`📍 ${place.display_name}`)
            .openPopup();
        } else {
        // fallback
        window.map.setView([lat, lon], 13);
        if (window.searchMarker) {
            window.map.removeLayer(window.searchMarker);
        }
        window.searchMarker = L.marker([lat, lon]).addTo(window.map)
            .bindPopup(`📍 ${place.display_name || 'الموقع'}`)
            .openPopup();
        }
    });
});

function highlightMatch(text, query) {
  if (!query) return text;

  const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  const regex = new RegExp(`(${escaped})`, 'gi');

  return text.replace(regex, '<span class="highlight">$1</span>');
}

function normalizeText(text) {
  return text
    .toLowerCase()
    .replace(/[أإآ]/g, 'ا')
    .replace(/ة/g, 'ه')
    .replace(/ى/g, 'ي')
    .replace(/\s+/g, '');
}

function fuzzyMatch(text, query) {
  const t = normalizeText(text);
  const q = normalizeText(query);

  return t.includes(q);
}

// ========================================
// ===== AUTOCOMPLETE SEARCH =====
// ========================================

const resultsBox = document.getElementById('search-results');
let searchTimeout = null;
searchInput?.addEventListener('input', function () {
  const query = this.value.trim();
  // ❌ إذا فاضي
  if (!query) {
    // 🔥 إخفاء النتائج
    resultsBox.style.display = 'none';
    // 🔥 حذف ماركر البحث فقط
    if (window.searchMarker) {
        window.map.removeLayer(window.searchMarker);
        window.searchMarker = null;
    }
    return;
  }

  // ⏳ Debounce (تخفيف الضغط)
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=sy&accept-language=ar&limit=5&q=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        if (!data || data.length === 0) {
          resultsBox.style.display = 'none';
          return;
        }
        resultsBox.innerHTML = '';
        // 🎯 فلترة + ترتيب احترافي
        const filtered = data.filter(place => {
          const type = place.type;
          const validType =
            type === 'city' ||
            type === 'town' ||
            type === 'village' ||
            type === 'administrative';
          const matches = fuzzyMatch(place.display_name, query);
          return validType && matches;
        });
        // fallback إذا ما في نتائج
        const finalResults = filtered.length > 0 ? filtered : data;
        // ترتيب (الأهم أولاً)
        finalResults.sort((a, b) => {
          const priority = {
            city: 1,
            town: 2,
            village: 3,
            administrative: 4
          };
          return (priority[a.type] || 10) - (priority[b.type] || 10);
        });
        function getPlaceIcon(type) {
          switch(type) {
            case 'city': return '🏙️';
            case 'town': return '🏘️';
            case 'village': return '🌿';
            case 'administrative': return '🗺️';
            default: return '📍';
          }
        }
        finalResults.forEach(place => {
          const div = document.createElement('div');
          div.className = 'search-item';
          const icon = getPlaceIcon(place.type);
          const nameParts = place.display_name.split(',');
          // 🎯 أهم جزئين فقط
          const mainName = nameParts[0];
          const secondary = nameParts[1] ? ' - ' + nameParts[1] : '';
          const finalText = mainName + secondary;
          div.innerHTML = `
            <span class="search-icon">${icon}</span>
            <span class="search-text">${highlightMatch(finalText, query)}</span>
          `;
          div.addEventListener('click', () => {
            const lat = parseFloat(place.lat);
            const lon = parseFloat(place.lon);
            if (place.boundingbox) {
              const south = parseFloat(place.boundingbox[0]);
              const north = parseFloat(place.boundingbox[1]);
              const west  = parseFloat(place.boundingbox[2]);
              const east  = parseFloat(place.boundingbox[3]);
              const bounds = [
                [south, west],
                [north, east]
              ];       
              window.map.fitBounds(bounds);
            } else {
              window.map.setView([lat, lon], 13);
            }
            // marker
            if (window.searchMarker) {
              window.map.removeLayer(window.searchMarker);
            }
            window.searchMarker = L.marker([lat, lon]).addTo(window.map)
              .bindPopup(`📍 ${place.display_name}`)
              .openPopup();
            // UX
            searchInput.value = place.display_name;
            resultsBox.style.display = 'none';
          });       
          resultsBox.appendChild(div);
        });
        resultsBox.style.display = 'block';
      });
  }, 400);; // تأخير بسيط
});

// ========================================
// ===== CLOSE SEARCH RESULTS =====
// ========================================

document.addEventListener('click', function (e) {
  const searchBox = document.getElementById('map-search');
  if (searchBox && !searchBox.contains(e.target)) {
    resultsBox.style.display = 'none';
  }
});