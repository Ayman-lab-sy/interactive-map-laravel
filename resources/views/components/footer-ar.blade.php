<footer class="footer">
  <p>جميع الحقوق محفوظة © 2026 - منظمة العلويين والأقليات السورية</p>

  <p>
    تواصل معنا:
    <a href="mailto:info@thealawites.com">info@thealawites.com</a>
    -
    <span dir="ltr">004368110868580</span>
  </p>

  <p>
    <a href="{{ url(app()->getLocale() . '/privacy-new') }}">سياسة الخصوصية</a>
  </p>

  <p>
    <a href="{{ url(app()->getLocale() . '/contact') }}">أرسل لنا رسالة</a>
  </p>

  <div class="social-links" style="margin-top: 15px;">
    <a href="https://facebook.com" target="_blank">
      <img src="{{ asset('assets/icons/facebook.svg') }}" alt="فيسبوك" width="24">
    </a>
    <a href="https://twitter.com" target="_blank">
      <img src="{{ asset('assets/icons/twitter.svg') }}" alt="تويتر" width="24">
    </a>
    <a href="https://instagram.com" target="_blank">
      <img src="{{ asset('assets/icons/instagram.svg') }}" alt="انستغرام" width="24">
    </a>
    <a href="https://t.me" target="_blank">
      <img src="{{ asset('assets/icons/telegram.svg') }}" alt="تلغرام" width="24">
    </a>
    <a href="https://www.tiktok.com/@yourusername" target="_blank">
      <img src="{{ asset('assets/icons/tiktok.svg') }}" alt="تيك توك" width="24">
    </a>
  </div>
</footer>

<!-- المساعد الذكي -->
<script src="https://cdn.jsdelivr.net/npm/fuse.js@6.6.2"></script>
<script>
  console.log("🔥 تم تحميل Fuse.js:", typeof Fuse !== 'undefined' ? 'نعم' : 'لا');
</script>

<script src="{{ asset('assets/js/synonyms.js') }}"></script>
<script src="{{ asset('assets/js/chat-assistant.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/chat-assistant.css') }}">

<div id="assistantHint"
     style="position: fixed; bottom: 110px; right: 90px; background-color: #7a001f;
            color: #fff; padding: 14px 18px; border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.4); font-size: 15px;
            z-index: 9999; max-width: 250px; display: none;
            animation: fadeIn 0.6s ease; border: 1px solid gold;">
  <span style="animation: pulse 1.2s infinite ease-in-out; display:inline-block; margin-left: 6px;">💬</span>
  أنا المساعد الذكي للمنظمة<br>
  اسألني عن التبرع أو الانضمام أو الدعم.
</div>

<script>
  window.addEventListener('load', () => {
    const hint = document.getElementById('assistantHint');
    const btn = document.getElementById('chat-icon');
    if (!hint || !btn) return;

    setTimeout(() => {
      hint.style.display = 'block';
      setTimeout(() => {
        hint.style.animation = 'fadeOut 0.5s ease';
        setTimeout(() => hint.style.display = 'none', 500);
      }, 8000);
    }, 4000);

    btn.addEventListener('click', () => {
      hint.style.display = 'none';
    });
  });
</script>
