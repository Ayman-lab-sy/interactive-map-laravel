@extends('layouts.main')

@section('title', 'أخبار المنظمة | منظمة العلويين')

@section('content')

<header class="hero">
  <img src="{{ asset('assets/logo.png') }}" alt="شعار المنظمة" class="logo">
  <h1>أخبار المنظمة</h1>
  <p>تابع أرشيف البيانات والأخبار الصادرة عن منظمة العلويين والأقليات السورية</p>
  <div class="hero-buttons">
    <a href="{{ url('/') }}" class="btn btn-outline">العودة للصفحة الرئيسية</a>
  </div>
</header>

<section class="section news-section">
  <h2>كل الأخبار</h2>
  <div class="grid" id="news-list"></div>
</section>

<script>
fetch('{{ asset("assets/data/news.json") }}')
  .then(res => res.json())
  .then(data => {
    const container = document.getElementById('news-list');
    data.reverse().forEach(news => {
      const card = document.createElement('div');
      card.className = 'card';

      let adminButtons = '';
      @if(session()->has('authenticated') && session('authenticated') === true)
        adminButtons = `
          <a href="/admin/edit-news.php?link=${news.link}" class="btn btn-outline">✏️ تعديل</a>
          <a href="/admin/delete-news.php?link=${news.link}" class="btn btn-outline" style="color:red"
             onclick="return confirm('هل أنت متأكد من حذف الخبر؟')">🗑 حذف</a>
        `;
      @endif

      card.innerHTML = `
        <img src="${news.image}" alt="${news.title}">
        <h3>${news.title}</h3>
        <small>${news.date}</small>
        <p>${news.summary}</p>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <a class="btn btn-outline" href="${news.link}" target="_blank">اقرأ المزيد</a>
          ${news.link_en ? `<a class="btn btn-outline" href="${news.link_en}" target="_blank">EN</a>` : ''}
          ${adminButtons}
        </div>
      `;
      container.appendChild(card);
    });
  });
</script>

@endsection
