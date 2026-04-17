<footer class="footer">
  <p>All rights reserved © 2025 - The Alawites & Syrian Minorities Organization</p>
  <p>Contact us: <a href="mailto:info@thealawites.com">info@thealawites.com</a> - <span dir="ltr">004368110868580</span></p>
  <p><a href="/en/privacy.php">Privacy Policy</a></p>
  <p><a href="/en/contact.php">Contact Us</a></p>
  <div class="social-links" style="margin-top: 15px;">
    <a href="https://facebook.com" target="_blank"><img src="/assets/icons/facebook.svg" alt="Facebook" width="24"></a>
    <a href="https://twitter.com" target="_blank"><img src="/assets/icons/twitter.svg" alt="Twitter" width="24"></a>
    <a href="https://instagram.com" target="_blank"><img src="/assets/icons/instagram.svg" alt="Instagram" width="24"></a>
    <a href="https://t.me" target="_blank"><img src="/assets/icons/telegram.svg" alt="Telegram" width="24"></a>
    <a href="https://www.tiktok.com/@yourusername" target="_blank"><img src="/assets/icons/tiktok.svg" alt="TikTok" width="24"></a>
  </div>
</footer>

<!-- Smart Assistant (optional to disable in EN) -->
<script src="https://cdn.jsdelivr.net/npm/fuse.js@6.6.2"></script>
<script src="/assets/js/synonyms.js"></script>
<script src="/assets/js/chat-assistant.js"></script>
<link rel="stylesheet" href="/assets/css/chat-assistant.css">

<div id="assistantHint" style="position: fixed; bottom: 110px; right: 90px; background-color: #7a001f; color: #fff; padding: 14px 18px; border-radius: 16px; box-shadow: 0 6px 20px rgba(0,0,0,0.4); font-size: 15px; z-index: 9999; max-width: 250px; display: none; animation: fadeIn 0.6s ease; border: 1px solid gold;">
  <span style="animation: pulse 1.2s infinite ease-in-out; display:inline-block; margin-left: 6px;">💬</span>
  I’m the smart assistant. Ask me about donations, joining, or support.
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
<script>
  const hideAssistant = () => {
    const chatBtn = document.getElementById("chat-assistant-button");
    const assistantBox = document.getElementById("chat-box");
    const assistantHint = document.getElementById("assistantHint");

    if (chatBtn) chatBtn.style.display = "none";
    if (assistantBox) assistantBox.style.display = "none";
    if (assistantHint) assistantHint.style.display = "none";
  };

  // حاول أولاً
  hideAssistant();

  // ثم راقب DOM لأي إضافات جديدة
  const observer = new MutationObserver(hideAssistant);
  observer.observe(document.body, { childList: true, subtree: true });
</script>


