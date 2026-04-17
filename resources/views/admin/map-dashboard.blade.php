<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم</title>

    <style>
        body {
            font-family: Arial;
            background: #0f172a;
            color: #fff;
            padding: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .card {
            background: #1e293b;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }

        .title {
            font-size: 14px;
            opacity: 0.7;
        }

        .value {
            font-size: 28px;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2>📊 لوحة التحكم</h2>

<div style="
  display:flex;
  gap:10px;
  margin-bottom:20px;
  flex-wrap:wrap;
">

  <!-- فلتر الزمن -->
  <select id="rangeFilter">
    <option value="today">اليوم</option>
    <option value="week">هذا الأسبوع</option>
    <option value="month" selected>هذا الشهر</option>
  </select>

  <!-- فلتر المحافظة -->
  <select id="govFilter">
    <option value="">كل المحافظات</option>
    <option value="دمشق">دمشق</option>
    <option value="ريف دمشق">ريف دمشق</option>
    <option value="حلب">حلب</option>
    <option value="حمص">حمص</option>
    <option value="حماة">حماة</option>
    <option value="اللاذقية">اللاذقية</option>
    <option value="طرطوس">طرطوس</option>
    <option value="درعا">درعا</option>
    <option value="السويداء">السويداء</option>
    <option value="القنيطرة">القنيطرة</option>
    <option value="دير الزور">دير الزور</option>
    <option value="الحسكة">الحسكة</option>
    <option value="الرقة">الرقة</option>
    <option value="إدلب">إدلب</option>
  </select>

  <!-- فلتر النوع -->
  <select id="categoryFilter">
    <option value="">كل الأنواع</option>
    <option value="قتل">قتل</option>
    <option value="اعتقال">اعتقال</option>
    <option value="تهجير">تهجير</option>
    <option value="انفجار">انفجار</option>
    <option value="حادث">حادث</option>
    <option value="اختطاف">اختطاف</option>
    <option value="اطلاق نار">اطلاق نار</option>
    <option value="مفقود">مفقود</option>
    <option value="فصل">فصل</option>
    <option value="سطو">سطو</option>
    <option value="اقتحام">اقتحام</option>
    <option value="قصف">قصف</option>
  </select>

  <!-- فلتر الأيام -->
  <input
    type="number"
    id="daysFilter"
    value="30"
    min="1"
    max="365"
    style="width:100px;"
    placeholder="أيام"
  >

</div>

<div class="grid">
    <div class="card">
        <div class="title">إجمالي الأحداث</div>
        <div class="value" id="total">0</div>
    </div>

    <div class="card">
        <div class="title">اليوم</div>
        <div class="value" id="today">0</div>
    </div>

    <div class="card">
        <div class="title">هذا الأسبوع</div>
        <div class="value" id="week">0</div>
    </div>

    <div class="card">
        <div class="title">هذا الشهر</div>
        <div class="value" id="month">0</div>
    </div>
</div>

<div id="trendBox" style="
  margin-top:20px;
  background:#1e293b;
  padding:15px;
  border-radius:10px;
  text-align:center;
  font-size:18px;
"></div>

<div style="margin-top:40px;">
    <canvas id="categoryChart"></canvas>
</div>

<div style="margin-top:50px;">
    <canvas id="timelineChart"></canvas>
</div>

<div style="margin-top:40px;">
    <h3>🔥 أخطر المحافظات</h3>
    <ul id="topGovs"></ul>
</div>

<script>
    window.locale = "{{ app()->getLocale() }}";
</script>

<script>
let categoryChart;
let timelineChart;

function loadDashboard() {

  const range = document.getElementById('rangeFilter').value;
  const gov = document.getElementById('govFilter').value;
  const category = document.getElementById('categoryFilter').value;
  const days = document.getElementById('daysFilter').value;

  let url = `/api/events/stats?range=${range}`;

  if (gov) {
    url += `&governorate=${encodeURIComponent(gov)}`;
  }

  if (category) {
    url += `&category=${encodeURIComponent(category)}`;
  }

  if (days) {
    url += `&days=${days}`;
  }

  fetch(url)
    .then(res => res.json())
    .then(data => {

      // 🔥 حالياً خليه بس total
      document.getElementById('total').innerText = data.total;

      // (اختياري حالياً)
      document.getElementById('today').innerText = data.today ?? '-';
      document.getElementById('week').innerText = data.week ?? '-';
      document.getElementById('month').innerText = data.month ?? '-';

      renderCharts(data);
    });
}

function renderCharts(data) {

  const selectedCategory = document.getElementById('categoryFilter').value;
  const trendBox = document.getElementById('trendBox');
  if (trendBox) {
    trendBox.innerHTML = '';
  }

  if (selectedCategory && data.previous !== null) {

    const diff = data.total - data.previous;

    let percent = 0;
    if (data.previous > 0) {
      percent = Math.round((diff / data.previous) * 100);
    }

    let color = '#ccc';
    let arrow = '➖';

    if (diff > 0) {
      color = '#ff4d4d';
      arrow = '🔺';
    } else if (diff < 0) {
      color = '#00ff99';
      arrow = '🔻';
    }

    trendBox.innerHTML = `
      <div>
        📊 عدد ${selectedCategory}: <b>${data.total}</b>
      </div>

      <div style="margin-top:10px; color:${color}">
        ${arrow} مقارنة بالفترة السابقة: ${diff} (${percent}%)
      </div>
    `;
  }

  if (data.top_governorate) {
    trendBox.innerHTML += `
        <div style="margin-top:10px;">
        🔥 الأكثر تضرراً: ${data.top_governorate.governorate} (${data.top_governorate.count})
        </div>
    `;
  }

  if (data.by_category_percent && !selectedCategory) {
    trendBox.innerHTML += `<div style="margin-top:10px;">📊 التوزيع:</div>`;

    data.by_category_percent.forEach(item => {
        trendBox.innerHTML += `
        <div>
            ${item.category}: ${item.percent}%
        </div>
        `;
    });
  }

  if (data.previous !== null && data.previous > 0) {
    const growth = ((data.total - data.previous) / data.previous) * 100;

    if (growth > 50) {
        trendBox.innerHTML += `
        <div style="color:red; margin-top:10px;">
            ⚠️ ارتفاع خطير (+${Math.round(growth)}%)
        </div>
        `;
    }
  }

  if (data.timeline.length > 0) {

    const maxDay = data.timeline.reduce((max, item) => {
      return item.count > max.count ? item : max;
    }, data.timeline[0]);

    trendBox.innerHTML += `
      <div style="margin-top:10px;">
        📅 أعلى نشاط: ${maxDay.date} (${maxDay.count})
      </div>
    `;
  }

  const days = document.getElementById('daysFilter').value;

  if (days && data.total) {
    const avg = (data.total / days).toFixed(2);

    trendBox.innerHTML += `
      <div style="margin-top:10px;">
        📈 معدل يومي: ${avg}
      </div>
    `;
  }

  // حذف القديم
  if (categoryChart) categoryChart.destroy();
  if (timelineChart) timelineChart.destroy();

  // ❗ إذا ما في فلتر نوع → ارسم pie
  if (!selectedCategory) {
    categoryChart = new Chart(document.getElementById('categoryChart'), {
      type: 'pie',
      data: {
        labels: Object.keys(data.by_category),
        datasets: [{
          data: Object.values(data.by_category)
        }]
      }
    });
  }

  // ✔️ دائماً ارسم timeline
  timelineChart = new Chart(document.getElementById('timelineChart'), {
    type: 'line',
    data: {
      labels: data.timeline.map(item => item.date),
      datasets: [{
        label: 'عدد الأحداث',
        data: data.timeline.map(item => item.count),
        tension: 0.4,
        fill: true
      }]
    }
  });

  // المحافظات
  const list = document.getElementById('topGovs');
  list.innerHTML = '';

  data.top_governorates.forEach(item => {
    const li = document.createElement('li');
    li.innerHTML = `<a href="/${window.locale}/map?gov=${item.governorate}" style="color:#00d4ff;">
      ${item.governorate} – ${item.count}
    </a>`;
    list.appendChild(li);
  });
}

// أول تحميل
loadDashboard();

// تغيير الفلتر
document.getElementById('rangeFilter').addEventListener('change', loadDashboard);
document.getElementById('govFilter').addEventListener('change', loadDashboard);
document.getElementById('categoryFilter').addEventListener('change', loadDashboard);
document.getElementById('daysFilter').addEventListener('input', loadDashboard);
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>