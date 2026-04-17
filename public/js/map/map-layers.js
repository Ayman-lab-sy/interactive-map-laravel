window.heatLayer = L.heatLayer([], {
  radius: 35,       // 🔥 انتشار أكبر
  blur: 25,         // 🔥 نعومة أعلى
  maxZoom: 12,
  minOpacity: 0.4,

  // 🎯 Gradient احترافي
  gradient: {
    0.0: '#00ff00',   // أخضر
    0.3: '#ffff00',   // أصفر
    0.6: '#ff9900',   // برتقالي
    1.0: '#ff0000'    // أحمر
  }
});

window.markersCluster = L.markerClusterGroup({
  
  iconCreateFunction: function(cluster) {

    let count = cluster.getChildCount();
    const color = window.getColorByCount(count);

    // 🎯 حجم ديناميكي
    let size = 40;
    if (count > 100) size = 70;
    else if (count > 50) size = 60;
    else if (count > 20) size = 50;

    return L.divIcon({
      html: `
        <div class="cluster-inner" style="
          width:${size}px;
          height:${size}px;
          border-radius:50%;

          background: radial-gradient(circle at center, ${color}, #000);

          display:flex;
          align-items:center;
          justify-content:center;

          color:#111;
          text-shadow:
            0 0 4px rgba(255,255,255,0.9),
            0 0 8px rgba(255,255,255,0.7),
            0 0 12px rgba(255,255,255,0.5);

          font-weight:bold;
          font-size:${size / 3}px;

          border:2px solid rgba(255,255,255,0.6);

          box-shadow:
            0 0 10px ${color},
            0 0 20px ${color},
            0 0 40px ${color};

          transition: all 0.3s ease;
        ">
          ${count}
        </div>
      `,
      className: 'custom-cluster',
      iconSize: L.point(size, size)
    });
  }
});

window.governoratesLayer = L.layerGroup();

window.map.addLayer(window.markersCluster);
//window.markersCluster.setZIndex(10);

// ========================================
// ===== CLUSTER HOVER (JS FIX) =====
// ========================================

window.markersCluster.on('clustermouseover', function (a) {
  const el = a.layer._icon;
  if (!el) return;

  el.style.zIndex = 1000;

  el.style.boxShadow = `
    0 0 15px rgba(255,255,255,0.8),
    0 0 30px ${window.getColorByCount(a.layer.getChildCount())},
    0 0 60px ${window.getColorByCount(a.layer.getChildCount())}
  `;
});

window.markersCluster.on('clustermouseout', function (a) {
  const el = a.layer._icon;
  if (!el) return;

  el.style.zIndex = 1;

  el.style.boxShadow = `
    0 0 10px ${window.getColorByCount(a.layer.getChildCount())},
    0 0 20px ${window.getColorByCount(a.layer.getChildCount())},
    0 0 40px ${window.getColorByCount(a.layer.getChildCount())}
  `;
});

// ========================================
// ===== 🎨 COLOR SYSTEM (Design System)
// ========================================

const COLOR_SCALE = {
  none: '#1e293b',     // لا يوجد بيانات
  low: '#22c55e',      // أخضر
  medium: '#facc15',   // أصفر
  high: '#f97316',     // برتقالي
  critical: '#ef4444'  // أحمر
};

window.getColorByCount = function(count) {
  const max = Math.max(...Object.values(window.govCounts), 1);
  const ratio = Math.pow(count / max, 0.5);

  // 🎯 Gradient من أخضر → أصفر → برتقالي → أحمر
  let r, g, b;

  if (ratio < 0.5) {
    // أخضر → أصفر
    r = Math.floor(255 * (ratio * 2));
    g = 255;
  } else {
    // أصفر → أحمر
    r = 255;
    g = Math.floor(255 * (1 - (ratio - 0.5) * 2));
  }

  b = 0;

  return `rgb(${r}, ${g}, ${b})`;
}