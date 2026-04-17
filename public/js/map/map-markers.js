// ========================================
// ===== RENDER EVENTS =====
// ========================================

function createMarker(event) {
  if (event.isGovernorate) {
    const count = event.count;
    const color = window.getColorByCount(count);
    let size = 40;
    if (count > 100) size = 70;
    else if (count > 50) size = 60;
    else if (count > 20) size = 50;
    const marker = L.marker([event.lat, event.lng], {
      icon: L.divIcon({
        html: `
          <div class="cluster-inner" style="
            width:${size}px;
            height:${size}px;
            border-radius:50%;
            background: radial-gradient(circle at center, ${color}, #000);
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:bold;
            font-size:${size/3}px;
          ">
            ${count}
          </div>
        `,
        className: 'custom-cluster',
        iconSize: L.point(size, size)
      })
    });
    marker._popupOpened = false;
    marker.on('click', function () {

      // 1️⃣ تغيير المود
      window.viewMode = 'points';
      const btn = document.getElementById('toggle-view-mode');
      if (btn) {
        btn.classList.remove('active');
        btn.innerText = "🧭 وضع المحافظات";
      }

      // 2️⃣ تحديد المحافظة
      window.govParam = event.id.replace('gov-', '');

      document.getElementById('filter').value = 'all';

      // 3️⃣ reset cache
      window.lastBounds = null;

      // 4️⃣ zoom
      window.map.setView([event.lat, event.lng], 9);

      // 5️⃣ تحميل الأحداث
      loadEvents();
    });

    return marker;
  }

  let category = event.category ? event.category.trim() : '';

  // ======================
  // ICON
  // ======================
  const iconMap = {
    'قتل': '/icons/kill.png',
    'اعتقال': '/icons/arrest.png',
    'تهجير': '/icons/displacement.png',
    'انفجار': '/icons/explosion.png',
    'حادث': '/icons/accident.png',
    'اختطاف': '/icons/abduction.png',
    'اطلاق نار': '/icons/shooting.png',
    'مفقود': '/icons/missing.png',
    'فصل': '/icons/termination.png',
    'سطو': '/icons/theft.png',
    'اقتحام': '/icons/raid.png',
    'قصف': '/icons/bombing.png'
  };

  let iconUrl = '/icons/default.png';

  for (const key in iconMap) {
    if (category.includes(key)) {
      iconUrl = iconMap[key];
      break;
    }
  }

  const isMobile = window.innerWidth <= 768;

  let confidenceClass = 'low';

  if (event.confidence_level === 'high') {
    confidenceClass = 'high';
  } else if (event.confidence_level === 'medium') {
    confidenceClass = 'medium';
  }
  const defaultIcon = L.divIcon({
    html: `
      <div class="custom-marker ${confidenceClass}">
        <img src="${iconUrl}" />
      </div>
    `,
    className: '',
    iconSize: isMobile ? [60, 60] : [50, 50] // 👈 هون التعديل
  });

  const hoverIcon = L.divIcon({
    html: `
      <div class="custom-marker hover ${confidenceClass}">
        <img src="${iconUrl}" />
      </div>
    `,
    className: '',
    iconSize: isMobile ? [70, 70] : [70, 70] // ممكن تتركها نفسها أو تكبرها شوي
  });

  const marker = L.marker([event.lat, event.lng], {
    icon: defaultIcon,
    category: category,
    riseOnHover: true // 🔥 هذا مهم
  });

  // ======================
  // COLOR (نفس القديم)
  // ======================
  let color;
  switch(category) {
    case 'قتل': color = 'red'; break;
    case 'اعتقال': color = 'Turquoise'; break;
    case 'تهجير': color = 'blue'; break;
    case 'انفجار': color = 'Violet'; break;
    case 'حادث': color = 'gray'; break;
    case 'اختطاف': color = 'Pink'; break;
    case 'اطلاق نار': color = 'orange'; break;
    case 'مفقود': color = 'maroon'; break;
    case 'فصل': color = 'Lilac'; break;
    case 'سطو': color = 'brown'; break;
    case 'اقتحام': color = 'purple'; break;
    case 'قصف': color = 'green'; break;
    default: color = 'gray';
  }

  // ======================
  // POPUP
  // ======================
  marker.bindPopup(`
    <div class="popup-container" id="popup-${event.id}">
      <div style="padding:10px; text-align:center;">
        ⏳ جاري تحميل التفاصيل...
      </div>
    </div>
  `);


  marker.on('popupopen', function () {
    const el = marker.getElement();
    if (el) {
      const inner = el.querySelector('.custom-marker');
      if (inner) {
        inner.classList.add('active');
        inner.classList.remove('burst');
        setTimeout(() => {
          inner.classList.add('burst');
          setTimeout(() => {
            inner.classList.remove('burst');
          }, 800); // نفس مدة animation
        }, 20);
      }
    }
    // إذا البيانات موجودة مسبقاً → لا نعيد الطلب
    if (
      window.allMarkersMap &&
      window.allMarkersMap[event.id] &&
      window.allMarkersMap[event.id].details
    ) {
      renderPopup(window.allMarkersMap[event.id].details, event.id);
      return;
    }
    fetch(`/api/events/${event.id}`)
      .then(res => res.json())
      .then(data => {
        let color;
        switch(data.category) {
          case 'قتل': color = 'red'; break;
          case 'اعتقال': color = 'Turquoise'; break;
          case 'تهجير': color = 'blue'; break;
          case 'انفجار': color = 'Violet'; break;
          case 'حادث': color = 'gray'; break;
          case 'اختطاف': color = 'Pink'; break;
          case 'اطلاق نار': color = 'orange'; break;
          case 'مفقود': color = 'maroon'; break;
          case 'فصل': color = 'Lilac'; break;
          case 'سطو': color = 'brown'; break;
          case 'اقتحام': color = 'purple'; break;
          case 'قصف': color = 'green'; break;
          default: color = 'gray';
        }
        const el = marker.getElement();
        if (el) {
          const inner = el.querySelector('.custom-marker');

          if (inner) {

            // تنظيف
            inner.classList.remove(
              'burst-قتل',
              'burst-اعتقال',
              'burst-تهجير',
              'burst-انفجار',
              'burst-حادث',
              'burst-اختطاف',
              'burst-اطلاق-نار',
              'burst-مفقود',
              'burst-فصل',
              'burst-سطو',
              'burst-اقتحام',
              'burst-قصف'
            );

            // تحويل الاسم لكلاس
            const safeCategory = data.category.replace(/\s+/g, '-');

            inner.classList.add(`burst-${safeCategory}`);
          }
        }
        if (!window.allMarkersMap[event.id]) {
          window.allMarkersMap[event.id] = {};
        }
        window.allMarkersMap[event.id].details = data;
        renderPopup(data, event.id);
      });
  });

  marker.on('popupclose', function () {
    const el = marker.getElement();
    if (el) {
      const inner = el.querySelector('.custom-marker');
      if (inner) {
        inner.classList.remove('active');
        inner.classList.remove('burst');

        // 🔥 نحذف كل ألوان burst
        inner.classList.remove(
          'burst-قتل',
          'burst-اعتقال',
          'burst-تهجير',
          'burst-انفجار',
          'burst-حادث',
          'burst-اختطاف',
          'burst-اطلاق-نار',
          'burst-مفقود',
          'burst-فصل',
          'burst-سطو',
          'burst-اقتحام',
          'burst-قصف'
        );
      }
    }
  });

  // ======================
  // FOCUS EVENT (نفس القديم)
  // ======================
  if (window.selectedLat && window.selectedLng) {
    if (
      Math.abs(event.lat - window.selectedLat) < 0.0001 &&
      Math.abs(event.lng - window.selectedLng) < 0.0001
    ) {
      window.map.setView([event.lat, event.lng], 10);
      marker.openPopup();
    }
  }

  if (!window.allMarkersMap) {
    window.allMarkersMap = {};
  }

  window.allMarkersMap[String(event.id)] = {
    ...event,
    marker: marker
  };
  
  return marker;
}


function renderPopup(data, eventId) {

  let color;
  switch(data.category) {
    case 'قتل': color = 'red'; break;
    case 'اعتقال': color = 'Turquoise'; break;
    case 'تهجير': color = 'blue'; break;
    case 'انفجار': color = 'Violet'; break;
    case 'حادث': color = 'gray'; break;
    case 'اختطاف': color = 'Pink'; break;
    case 'اطلاق نار': color = 'orange'; break;
    case 'مفقود': color = 'maroon'; break;
    case 'فصل': color = 'Lilac'; break;
    case 'سطو': color = 'brown'; break;
    case 'اقتحام': color = 'purple'; break;
    case 'قصف': color = 'green'; break;
    default: color = 'gray';
  }

  let confidenceHtml = '';

  if (data.confidence_level === 'high') {
    confidenceHtml = '🟢 موثوقية عالية';
  } else if (data.confidence_level === 'medium') {
    confidenceHtml = '🟡 موثوقية متوسطة';
  } else {
    confidenceHtml = '🔴 غير مؤكدة';
  }
  const popupContent = `
    <div class="popup-container">
      <div class="popup-title">
        📍 ${data.title}
      </div>
      <div class="popup-date">
        🕒 ${data.event_date ? formatDate(data.event_date) : ''}
      </div>
      <div class="popup-category" style="background:${color}">
        ${data.category}
      </div>
      ${data.image ? `
        <img src="/storage/${data.image}" class="popup-image">
      ` : ''}
      <div class="popup-description">
        ${data.description || ''}
      </div>
      ${data.video_url ? `
        <div class="popup-video">
          <a href="${data.video_url}" target="_blank" class="video-btn">
            ▶ مشاهدة الفيديو
          </a>
        </div>
      ` : ''}
      <div class="popup-confidence">
        ${confidenceHtml}
      </div>
      <div class="popup-meta">
        عدد المصادر: ${data.sources_count || 0}
      </div>
      <div class="popup-actions">
        <button class="btn-share" onclick="shareEvent(${data.id})">
          🔗 مشاركة
        </button>
      </div>
    </div>
    ${data.id && window.isAdmin ? `
      <div class="popup-actions">
        <button class="btn-share" onclick="shareEvent(${data.id})">
          🔗 مشاركة
        </button>
        ${data.id && window.isAdmin ? `
          <a href="/${window.locale}/edit-event/${data.id}" class="btn-edit">
            ✏️ تعديل
          </a>
          <form method="POST" action="/${window.locale}/events/${data.id}">
            <input type="hidden" name="_token" value="${window.csrf}">
            <input type="hidden" name="_method" value="DELETE">

            <button type="submit" class="btn-delete">
              🗑️ حذف
            </button>
          </form>
        ` : ''}
      </div>
    ` : ''}
  `;
  document.getElementById(`popup-${eventId}`).innerHTML = popupContent;
}

// ========================================
// ===== HELPER FUNCTIONS =====
// ========================================
function formatDate(dateString) {
  const d = new Date(dateString);
  const day = String(d.getDate()).padStart(2, '0');
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const year = d.getFullYear();
  const hours = String(d.getHours()).padStart(2, '0');
  const minutes = String(d.getMinutes()).padStart(2, '0');
  return `${day}-${month}-${year} ${hours}:${minutes}`;
}

function shareEvent(id) {
  let url = `${window.location.origin}/${window.locale}/event/${id}`;

  // 🔥 قراءة الفلتر الحالي
  const filter = document.getElementById('filter')?.value;

  if (filter === 'custom') {
    const from = document.getElementById('date-from')?.value;
    const to = document.getElementById('date-to')?.value;

    if (from && to) {
      url += `?from=${from}&to=${to}`;
    }
  } else if (filter && filter !== 'all') {
    url += `?range=${filter}`;
  }
  if (navigator.share) {
    navigator.share({
      title: "حدث مهم",
      text: "شاهد هذا الحدث",
      url: url
    }).catch(() => {});
  } else {
    navigator.clipboard.writeText(url);
    alert("تم نسخ الرابط");
  }
}