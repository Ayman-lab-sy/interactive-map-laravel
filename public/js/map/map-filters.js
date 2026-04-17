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
  if (window.viewMode !== 'points') {
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

  window.filteredEvents = filtered;
  const currentMode = window.smartView || window.viewMode;
  if (currentMode === 'points') {
    window.renderPoints(filtered);
  } else {
    window.renderGovernorates(filtered);
  }
  window.isFiltering = false;
}