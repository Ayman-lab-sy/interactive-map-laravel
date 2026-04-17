<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <link rel="icon" href="{{ asset('storage/favicon-32x32.png') }}" type="image/png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>{{ setting('site.title') }}</title>
    <meta name="description" content="{{ setting('site.meta_description') }}">
    <meta name="keywords" content="{{ setting('site.meta_keywords') }}">

    <!-- Open Graph (Social Media) -->
    <meta property="og:title" content="{{ setting('site.title') }}">
    <meta property="og:description" content="{{ setting('site.meta_description') }}">
    <meta property="og:image" content="{{ asset('storage/' . setting('site.logo')) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ setting('site.title') }}">
    <meta name="twitter:description" content="{{ setting('site.meta_description') }}">
    <meta name="twitter:image" content="{{ asset('storage/' . setting('site.logo')) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/layout.css') }}" rel="stylesheet" type="text/css" media="all">

    @if ($locale == 'ar')
        <link href="{{ asset('assets/rtl.css') }}" rel="stylesheet" type="text/css" media="all">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SS3C44S9NW"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-SS3C44S9NW');
    </script>
</head>

<body id="top" dir="{{ $locale == 'ar' ? 'rtl' : 'ltr' }}">

    <div class="bgded overlay padtop" style="background: @yield('headerImg')">

        <header id="header" class="hoc">
            <div id="logo" class="fl_left">

                <h1>
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">
                        <img src="{{ asset('storage/' . setting('site.logo')) }}" style="width: 8rem;" />
                        <span class="logo_name">{{ $site['title']->value }}</span>
                    </a>
                </h1>

            </div>
            <div style="display: flex; flex-direction:row; gap: .5rem;justify-content: end; align-items:center;">
                <nav id="mainav" class="fl_right">
                    <ul class="clear">
                        <li>
                            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">
                                {{ __('home.nav.home') }}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('about', ['locale' => app()->getLocale()]) }}">
                                {{ __('home.nav.about') }}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('site.page', ['slug' => 'our-goals', 'locale' => app()->getLocale()]) }}">
                                {{ __('home.nav.goals') }}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('news.new', ['locale' => app()->getLocale()]) }}">
                                {{ __('home.nav.news') }}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('donate', ['locale' => app()->getLocale()]) }}">
                                {{ __('home.nav.donate') }}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('join', ['locale' => app()->getLocale()]) }}">
                                {{ __('home.nav.join') }}
                            </a>
                        </li>

                    </ul>
                </nav>
                <span id="langnav" class="fl-right">
                    <ul class="clear" style="width: fit-content; display:flex;">
                        <li class="">
                            <a class="drop lang-nav" href="#">
                                <i class="fas fa-language "></i>
                            </a>
                            <ul style="padding: 0 !important;right: 0;">
                                @foreach (config('app.locales') as $loc)
                                    @php
                                        $url = request()->segments();
                                        $url[0] = $loc['value'];
                                    @endphp
                                    <li><a href="{{ url(implode('/', $url)) }}">{{ $loc['name'] }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </span>
            </div>

        </header>

        @yield('header')



    </div>

    @yield('main')


    <div class="wrapper row4">
        <footer id="footer" class="hoc clear">

            <div class="one_quarter first">
                <h6 class="heading">
                    <img src="{{ asset('storage/' . setting('site.logo_wide')) }}" style="width: 10rem;" />
                </h6>
                <ul class="faico clear">
                    @if ($sFooter)
                        @foreach ($sFooter as $key => $val)
                            <li><a class="faicon" href="{{ $val->value && $val->value[0] == '"' ? ltrim($val->value, '"') : $val->value }}" target="_blank"><i
                                        class="{{ $val->icon }}"></i></a></li>
                        @endforeach
                    @endif
                </ul>
            </div>
            @if ($cFooter)
                <div class="one_quarter">
                    <h6 class="heading">{{ __('home.contact') }}</h6>
                    <ul class="nospace linklist">
                        @foreach ($cFooter as $key => $gr)
                            @foreach ($gr as $v)
                                <li><i class="{{ $v->icon }}"></i> {{ $v->name }}: {{ $v->value && $v->value[0] == '"' ? ltrim($v->value, '"') : $v->value }}</li>
                            @endforeach
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="one_quarter">
                <h6 class="heading">{{ __('home.nav.nav') }}</h6>
                <ul class="nospace linklist">
                    <li>
                        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">
                            {{ __('home.nav.home') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('about', ['locale' => app()->getLocale()]) }}">
                            {{ __('home.nav.about') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('site.page', ['slug' => 'our-goals', 'locale' => app()->getLocale()]) }}">
                            {{ __('home.nav.goals') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('news.new', ['locale' => app()->getLocale()]) }}">
                            {{ __('home.nav.news') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('donate', ['locale' => app()->getLocale()]) }}">
                            {{ __('home.nav.donate') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('join', ['locale' => app()->getLocale()]) }}">
                            {{ __('home.nav.join') }}
                        </a>
                    </li>
                </ul>

            </div>
            <div class="one_quarter">
                <h6 class="heading">{{ __('home.about.title') }}</h6>
                <p class="nospace btmspace-15">{{ __('home.about.paragraph') }}</p>
            </div>
        </footer>
    </div>
    <div class="wrapper row5">
        <div id="copyright" class="hoc clear">

            <p class="fl_left">{{ __('home.copyright') }}</p>

        </div>
    </div>
    <a id="backtotop" href="#top"><i class="fas fa-chevron-up"></i></a>
    <!-- JAVASCRIPTS -->
    <script src="{{ asset('assets/scripts/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/scripts/jquery.backtotop.js') }}"></script>
    <script src="{{ asset('assets/scripts/jquery.mobilemenu.js') }}"></script>

    
    @if (request()->ip() == '84.115.219.100')
    <!-- مساعد الدردشة التجريبي -->
    <style>
    #chat-icon {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: #7b1113;
      color: white;
      border-radius: 50%;
      width: 55px;
      height: 55px;
      text-align: center;
      line-height: 55px;
      font-size: 24px;
      cursor: pointer;
      z-index: 9999;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    #chat-box {
      position: fixed;
      bottom: 90px;
      right: 20px;
      width: 300px;
      max-height: 400px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      display: none;
      flex-direction: column;
      overflow: hidden;
      z-index: 9998;
      font-family: 'Cairo', sans-serif;
    }

    #chat-box-header {
      background: #7b1113;
      color: white;
      padding: 10px;
      font-weight: bold;
    }

    #chat-box-messages {
      padding: 10px;
      flex: 1;
      overflow-y: auto;
      font-size: 14px;
    }

    #chat-box-input {
      display: flex;
      border-top: 1px solid #ccc;
    }

    #chat-box-input input {
      flex: 1;
      padding: 8px;
      border: none;
      font-family: inherit;
      font-size: 14px;
    }

    #chat-box-input button {
      background: #7b1113;
      color: white;
      border: none;
      padding: 0 15px;
      cursor: pointer;
    }
    </style>

    <div id="chat-icon"><i class="fas fa-comment-dots"></i></div>

    <div id="chat-box">
      <div id="chat-box-header">مساعد المنظمة</div>
      <div id="chat-box-messages"></div>
      <div id="chat-box-input">
        <input type="text" id="chat-input" placeholder="اكتب سؤالك...">
        <button onclick="handleUserInput()">إرسال</button>
      </div>
    </div>

    <script>
    const chatIcon = document.getElementById('chat-icon');
    const chatBox = document.getElementById('chat-box');
    const chatMessages = document.getElementById('chat-box-messages');
    const chatInput = document.getElementById('chat-input');

    chatIcon.addEventListener('click', () => {
      chatBox.style.display = chatBox.style.display === 'flex' ? 'none' : 'flex';
    });

    function handleUserInput() {
      const msg = chatInput.value.trim();
      if (!msg) return;
      appendMessage('أنت', msg);
      respondTo(msg);
      chatInput.value = '';
    }

    function appendMessage(sender, text) {
      const msgDiv = document.createElement('div');
      msgDiv.innerHTML = `<strong>${sender}:</strong> ${text}`;
      chatMessages.appendChild(msgDiv);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function respondTo(text) {
      const t = text.toLowerCase();
      let response = 'عذرًا، لم أفهم سؤالك. حاول استخدام كلمات مثل: تبرع، انضم، تواصل، مساعدة.';

      if (t.includes('انضم')) {
        response = 'للانضمام إلى المنظمة، يمكنك زيارة صفحة "انضم للمنظمة" من خلال الرابط في الأعلى.';
      } else if (t.includes('تبرع') || t.includes('دعم')) {
        response = 'للتبرع، يمكنك زيارة صفحة "قدم دعمك" وستجد فيها بيانات الحساب البنكي الرسمي.';
      } else if (t.includes('تواصل') || t.includes('راسل')) {
        response = 'للتواصل معنا، يمكنك استخدام معلومات الاتصال في أسفل الصفحة، أو مراسلتنا عبر البريد الإلكتروني.';
      } else if (t.includes('مساعدة') || t.includes('فورية')) {
        response = 'للحصول على مساعدة فورية، يُرجى استخدام وسائل الاتصال المباشرة الموجودة في صفحة "تواصل معنا".';
      }

      setTimeout(() => appendMessage('المساعد', response), 500);
    }
    </script>
    @endif
    
    <!-- IP DEBUG: {{ request()->ip() }} -->
</body>

</html>
