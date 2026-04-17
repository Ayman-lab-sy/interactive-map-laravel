function getGovernorateCenter(gov) {

    const coords = window.governorateCoordsArabic?.[gov];

    if (!coords) {
        console.warn('No coords for governorate:', gov);
        return [34.8, 38.0];
    }

    return coords;
}


// ==========================
// POINTS (بدون تغيير)
// ==========================
window.renderPoints = function(events) {

    window.markersCluster.clearLayers();
    if (!showMarkers) return;
    events.forEach(event => {
        if (!event.lat || !event.lng) return;
        // 🔥 إذا موجود مسبقاً لا تعيد إنشاؤه
        if (!window.allMarkersMap[event.id]) {
            const marker = createMarker(event);
            if (!marker) return;
            window.allMarkersMap[event.id] = {
                marker: marker,
                event: event
            };
        }
        // 🔥 دائماً أضف للكلستر
        window.markersCluster.addLayer(
            window.allMarkersMap[event.id].marker
        );
    });
};


// ==========================
// GOVERNORATES (المهم)
// ==========================
window.renderGovernorates = function(events) {
    window.governoratesLayer.clearLayers();
    const grouped = {};
    const data = window.filteredEvents || events;
    data.forEach(e => {
         if (!e.governorate) return;
        const govName =
        (window.govMap && window.govMap[e.governorate])
            ? window.govMap[e.governorate]
            : e.governorate;
        if (!govName) return; // 🔥 حماية
        if (!grouped[govName]) {
            grouped[govName] = [];
        }
        grouped[govName].push(e);
    });
    Object.keys(grouped).forEach(gov => {
        const count = grouped[gov].length;
        const latlng = getGovernorateCenter(gov);
        if (!latlng || latlng.length !== 2) return;
        const color = window.getColorByCount(count);
        let size = 50;
        if (count > 100) size = 70;
        else if (count > 50) size = 60;
        const marker = L.marker(latlng, {
            icon: L.divIcon({
                html: `
                    <div class="gov-cluster"
                        style="
                            width:${size}px;
                            height:${size}px;
                            background: radial-gradient(circle at center, ${color}, #000);
                            box-shadow:
                            0 0 10px ${color},
                            0 0 20px ${color};
                        ">
                        ${count}
                    </div>
                `,
                className: '',
                iconSize: [size, size]
            })
        });
        // 🔥 popup بسيط (مو event)
        marker.bindPopup(`
            <div style="padding:10px; font-family:Arial;">
                <b>📍 ${gov}</b><br>
                عدد الأحداث: ${count}
            </div>
        `);
        marker.on('click', () => {
            // 🔥 تحويل للوضع النقطي
            window.viewMode = 'points';
            // 🔥 تحديد المحافظة
            window.govParam = gov;
            // 🔥 reset الكاش (مهم جداً)
            window.lastBounds = null;
            window.lastFilter = null;
            window.lastGov = null;
            // 🔥 تحديث زر UI
            const btn = document.getElementById('toggle-view-mode');
            if (btn) {
                btn.classList.remove('active');
                btn.innerText = "🧭 وضع المحافظات";
            }
            // 🔥 زوم على المحافظة
            window.map.setView(latlng, 9);
            // 🚀 تحميل البيانات
            loadEvents();
        });
        window.governoratesLayer.addLayer(marker);
    });
};