let pendingExtra = null;
let waitingForExtraConfirmation = false;
let pendingFullAnswer = "";
let supportSessionActive = false;
let waitingForHumanApproval = false;
let supportConnected = false;

function showConnectingIndicator() {
  const messagesBox = document.getElementById('chat-box-messages');
  if (!messagesBox) {
    console.error('❌ chat-box-messages غير موجود');
    return;
  }

  if (document.getElementById('support-connecting')) return;

  const indicator = document.createElement('div');
  indicator.id = 'support-connecting';
  indicator.innerHTML = `
    <div class="support-connecting-box">
      <div class="support-spinner"></div>
      <div class="support-text">
        جارٍ الاتصال بفريق الدعم البشري
        <div class="support-dots">
          <span></span><span></span><span></span>
        </div>
      </div>
    </div>
  `;

  messagesBox.appendChild(indicator);
}


function hideConnectingIndicator() {
  const el = document.getElementById('support-connecting');
  if (el) el.remove();
}

function showSupportOnlineBadge() {
  if (document.getElementById('support-online-badge')) return;

  const inputBox = document.getElementById('chat-box-input');

  const badge = document.createElement('div');
  badge.id = 'support-online-badge';
  badge.innerHTML = '🟢 الدعم متصل الآن';

  inputBox.parentNode.insertBefore(badge, inputBox);
}

function hideSupportOnlineBadge() {
  const badge = document.getElementById('support-online-badge');
  if (badge) badge.remove();
}

document.addEventListener("DOMContentLoaded", function () {
  const body = document.body;

  const chatIcon = document.createElement("div");
  chatIcon.id = "chat-icon";
  chatIcon.innerHTML = '<i class="fas fa-robot"></i>';

  const chatBox = document.createElement("div");
  chatBox.id = "chat-box";
  chatBox.innerHTML = `
    <div id="chat-box-header">مساعد المنظمة</div>

    <div id="chat-box-messages"></div>

    <div id="chat-box-input">
      <div id="input-wrapper">
        <input type="text" id="chat-input" placeholder="اكتب سؤالك...">
        <button onclick="handleUserInput()">إرسال</button>
      </div>
    </div>
  `;


  body.appendChild(chatIcon);
  body.appendChild(chatBox);

  const chatMessages = document.getElementById('chat-box-messages');
  const chatInput = document.getElementById('chat-input');
  let assistantOpened = false;

  // 🎧 تحضير الصوت
  let notificationAudio;
  let audioReady = false;

  chatInput.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      handleUserInput();
    }
  });

  chatIcon.addEventListener('click', () => {
    const wasClosed = window.getComputedStyle(chatBox).display === 'none';

    if (wasClosed) {
      chatBox.style.display = 'flex';
      chatBox.style.flexDirection = 'column';
      chatBox.classList.add('show-animated');
      setTimeout(() => chatBox.classList.remove('show-animated'), 500);
    } else {
      chatBox.style.display = 'none';
    }

    chatIcon.classList.remove('unread');

    if (!audioReady) {
      notificationAudio = new Audio('/assets/notify.mp3');
      notificationAudio.load();
      audioReady = true;
    }

    if (wasClosed && !assistantOpened) {
      const hint = document.getElementById('assistantHint');
      if (hint) hint.style.display = 'none'
      assistantOpened = true;
      typeMessage('المساعد', `👋 أهلًا بك في موقع منظمة العلويين والأقليات السورية.
    أنا المساعد الذكي، جاهز أجاوبك على أي استفسار.
    جرب تسألني عن: التبرع، الانضمام، الدعم القانوني، التوثيق...
    أو اكتب "تواصل مباشر" وسأربطك مع فريق الدعم البشري.`);
    }
  });

  window.handleUserInput = function () {
    const msg = chatInput.value.trim();
    if (!msg) return;

    const lower = msg.toLowerCase();
    typeMessage('أنت', msg, 'user');
    chatInput.value = '';

    // 🟢 إذا الجلسة مفتوحة → كل الرسائل تذهب للدعم
    if (supportSessionActive) {
      sendMessageToSupport(msg);
      return;
    }

    if (waitingForHumanApproval) {
      typeMessage(
        "المساعد",
        "⏳ ما زلنا ننتظر موافقة فريق الدعم، الرجاء الانتظار وعدم تحديث الصفحة."
      );
      return;
    }

    // 🟡 طلب فتح جلسة دعم
    const directSupportTriggers = [
      "تواصل مباشر",
      "دعم بشري",
      "اريد التواصل مع فريق الدعم",
      "لم يتم حل مشكلتي",
      "ما حليت مشكلتي",
      "ما استفدت",
      "ما لقيت جواب",
      "لم افهم",
      "ما فهمت"
    ];

    if (directSupportTriggers.some(t => lower.includes(t))) {
      startDirectSupport(msg);
      return;
    }

    // 🔵 غير ذلك → مساعد ذكي
    respondTo(msg);
  };


  function startDirectSupport(firstMessage) {
    const questionId = "qid_" + Date.now();

    fetch("https://n8n.thealawites.com/webhook/direct-support", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        questionId,
        message: firstMessage,
        timestamp: new Date().toISOString()
      })
    })
    .then(() => {
      localStorage.setItem('lastQuestionId', questionId);

      waitingForHumanApproval = true;
      supportConnected = false;
      supportSessionActive = false;

      typeMessage(
        "المساعد",
        "⏳ سيتم وصلك مع فريق الدعم البشري.<br>الرجاء الانتظار وعدم تحديث الصفحة."
      );

      showConnectingIndicator();
    })
    .catch(() => {
      typeMessage("المساعد", "❌ حدث خطأ أثناء فتح جلسة الدعم.");
    });
  }

  function sendMessageToSupport(msg) {
    const sessionId = localStorage.getItem('lastQuestionId');
    if (!sessionId) {
      typeMessage("المساعد", "⚠️ لا توجد جلسة دعم فعّالة.");
      supportSessionActive = false;
      return;
    }

    fetch("https://n8n.thealawites.com/webhook/direct-support", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        sessionId,
        message: msg,
        timestamp: new Date().toISOString()
      })
    });
  }


  function expandQuestionWithSynonyms(originalText) {
    if (typeof window.synonyms !== "object") return originalText;

    let words = originalText.split(/\s+/);
    let expandedWords = words.flatMap(word => {
      const normalized = word.trim().toLowerCase();
      return window.synonyms[normalized]
        ? [normalized, ...window.synonyms[normalized]]
        : [normalized];
    });

    return expandedWords.join(" ");
  }

  function getWeight(word) {
    if (word.length <= 3) return 0;
    if (word.length <= 5) return 2;
    return 5;
  }

  async function respondTo(inputText) {
    console.log("🟡 inputText:", inputText);
    const input = inputText.toLowerCase().trim();
    const expandedInput = expandQuestionWithSynonyms(input);
    
    // 🔴 دعم بشري – أولوية قصوى (قبل أي تحليل ذكي)
    const directSupportTriggers = [
      "تواصل مباشر",
      "دعم بشري",
      "اريد التواصل مع فريق الدعم",
      "لم يتم حل مشكلتي",
      "ما حليت مشكلتي",
      "ما استفدت",
      "ما لقيت جواب",
      "ما فهمت"
    ];

    if (!supportSessionActive && directSupportTriggers.some(trigger => input.includes(trigger))) {
      setTimeout(() => {
        typeMessage(
          'المساعد',
          '📩 يمكنني مساعدتك في إيصال سؤالك للفريق المختص.'
        );
        setTimeout(offerDirectSupport, 800);
      }, 300);
      return;
    }

    try {
      const response = await fetch('/api/assistant/search', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ question: input })
      });

      const result = await response.json();

      if (result.answer && result.answer.trim() !== '') {
        setTimeout(() => {
          typeMessage('المساعد', result.answer);
        }, 400);
      } else {
        setTimeout(() => {
          typeMessage(
            'المساعد',
            'عذرًا، لم أفهم سؤالك. جرب كلمات مثل: تبرع، انضمام، توثيق، قانوني...'
          );
          saveUnanswered(inputText, expandedInput);
        }, 400);
      }

    } catch (e) {
      console.warn('❌ API error:', e);
      typeMessage('المساعد', '⚠️ حدث خطأ تقني، حاول لاحقًا.');
    }
  }

  function typeMessage(sender, fullHtml, senderType = 'assistant') {
    const isUser = senderType === 'user';
    const isSupport = senderType === 'support';

    const msgWrapper = document.createElement('div');
    msgWrapper.className = `chat-wrapper ${isUser ? 'chat-right' : 'chat-left'}`;

    const avatar = document.createElement('img');
    avatar.className = 'chat-avatar';
    avatar.src = isUser
      ? '/assets/avatar-user.png'
      : isSupport
      ? '/assets/avatar-support.png'
      : '/assets/avatar-bot-active.gif';

    const msgDiv = document.createElement('div');
    msgDiv.className = `chat-message ${isUser ? 'chat-user' : isSupport ? 'chat-support' : 'chat-assistant'}`;

    msgWrapper.appendChild(avatar);
    msgWrapper.appendChild(msgDiv);
    chatMessages.appendChild(msgWrapper);

    if (!isUser) {
      const replySound = new Audio('/assets/chat-pop.mp3');
      replySound.play().catch((err) => console.warn("🔇 تعذّر تشغيل الصوت تلقائيًا:", err));
      avatar.classList.add('pulse');
      setTimeout(() => avatar.classList.remove('pulse'), 700);
    }

    // ✅ الكتابة التدريجية مع دعم HTML داخل عناصر
    const lines = fullHtml.split(/\r?\n/).filter(l => l.trim() !== '');
    let currentLine = 0;
    let charIndex = 0;
    let fullDisplay = '';
    let speed = 20;

    function typeNextChar() {
      if (currentLine >= lines.length) {
        msgDiv.classList.remove('typing-cursor');
        if (!isUser && !isSupport) avatar.src = '/assets/avatar-bot-idle.gif';
        return;
      }

      const line = lines[currentLine];
      const current = line.slice(0, charIndex++);

      const cursor = (charIndex <= line.length) ? '<span style="color:#fff;">|</span>' : '';
      msgDiv.innerHTML =`<div style="direction: rtl; text-align: right;">${highlightImportantWords(fullDisplay + current + cursor)}</div>`;
      chatMessages.scrollTop = chatMessages.scrollHeight;

      if (charIndex <= line.length) {
        setTimeout(typeNextChar, speed);
      } else {
        fullDisplay += line + '<br>';
        currentLine++;
        charIndex = 0;
        setTimeout(typeNextChar, 100); // تأخير بسيط بين الأسطر
      }
    }

    typeNextChar();
  }

  function highlightImportantWords(text) {
    const keywords = ['التبرع', 'الانتهاكات', 'الدعم الإنساني', 'التمثيل الدولي', 'المكونات', 'الفئات', 'الأقليات السورية', 'توثيق', 'الانضمام', 'الدعم القانوني', 'الدعم البشري', 'التوثيق', 'انتهاك', 'المساعدة', 'تواصل مباشر'];
    keywords.forEach(word => {
      const regex = new RegExp(`(?<!\\w)(${word})(?!\\w)`, 'gi');
      text = text.replace(regex, '<strong class="highlight-word">$1</strong>');
    });
    return text;
  }

  function checkForReply(questionId) {
    fetch('/assistant-replies.json?_=' + Date.now())
      .then(res => res.json())
      .then(replies => {
        const relevantReplies = replies.filter(r => r.questionId === questionId);

        relevantReplies.forEach((r, i) => {
          const key = `replied_${questionId}_${i}`;
          if (!localStorage.getItem(key)) {
            let cleanAnswer = r.answer.trim().replace(/^\/?رد\s*/i, '');
            
            if (r.type === 'session_ended') {
              supportSessionActive = false;
              localStorage.removeItem('lastQuestionId');
              hideSupportOnlineBadge();

              chatInput.disabled = false;
              document.getElementById('input-wrapper')
                .classList.remove('input-wrapper-active');

              typeMessage(
                'المساعد',
                '✔️ تم إنهاء جلسة الدعم بنجاح.<br>نشكر تواصلك معنا. إذا احتجت لأي مساعدة إضافية، يمكنك مراسلتنا في أي وقت.'
              );

              const box = document.querySelector('.support-reply-box');
              if (box && box.parentElement) box.parentElement.remove();

              return;
            }

            typeMessage('الدعم البشري', cleanAnswer, 'support');

            localStorage.setItem(key, '1');
            chatIcon.classList.add('unread');

            if (notificationAudio) {
              notificationAudio.play().catch(err => {
                console.warn("🔇 لم يُسمح بتشغيل الصوت تلقائيًا:", err);
              });
            }
          }
        });
      })
      .catch(() => {
        console.error("❌ حدث خطأ أثناء محاولة التحقق من الرد.");
      });
  }
  
  function checkSupportStatus(questionId) {
    fetch('/assistant-status.json?_=' + Date.now())
      .then(res => res.json())
      .then(status => {
        if (!supportConnected && status[questionId]?.connected === true) {
          supportConnected = true;
          supportSessionActive = true;
          waitingForHumanApproval = false;
          showSupportOnlineBadge();
          chatInput.disabled = false;
          document.getElementById('input-wrapper')
            .classList.add('input-wrapper-active');

          hideConnectingIndicator();
        }
      })
      .catch(() => {
        console.warn('تعذر قراءة assistant-status.json');
      });
  }

  setInterval(() => {
    const qid = localStorage.getItem('lastQuestionId');
    if (qid) {
      checkSupportStatus(qid);
      checkForReply(qid);
    }
  }, 5000);

});
