@extends('components.layout')

@section('headerImg', 'url(' . asset('storage/' . setting('site.headerImage')) . ');')

@section('header')
    <div id="pageintro" class="hoc clear">
        <article>

            <h3 class="heading">{{ $sets['home']['heading']['title']['value'] }}</h3>
            <p>{{ $sets['home']['heading']['paragraph']['value'] }}</p>
            <footer>
                <ul class="nospace inline pushright">
                    <li><a class="btn" href="{{ route('donate') }}">{{ __('home.nav.donate') }}</a></li>
                    <li><a class="btn inverse" href="{{ route('join') }}">{{ __('home.nav.join') }}</a></li>
                </ul>
            </footer>
        </article>
    </div>

    <div class="wrapper row1">
        <section id="ctdetails" class="hoc clear">
            <ul class="nospace clear">
                @foreach ($cHeader as $k => $gr)
                    @foreach ($gr as $ch)
                        @if ($k === 'social')
                            <li class="one_quarter">
                                <div class="block clear">
                                    <a href="{{ $ch->value && $ch->value[0] == '"' ? ltrim($ch->value, '"') : $ch->value }}" target="_blank"><i class="{{ $ch->icon }}"></i></a>
                                    <a style="color: #fff;" target="_blank" href="{{ $ch->value && $ch->value[0] == '"' ? ltrim($ch->value, '"') : $ch->value }}"><strong>{{ $ch->name }}</strong></a>
                                </div>
                            </li>
                        @else
                            <li class="one_quarter">
                                <div class="block clear">
                                    <a href="#"><i class="{{ $ch->icon }}"></i></a>
                                    <span style="color: #fff;"><strong>{{ $ch->name }}</strong>{{ $ch->value && $ch->value[0] == '"' ? ltrim($ch->value, '"') : $ch->value }}</span>
                                </div>
                            </li>
                        @endif
                    @endforeach
                @endforeach
                @foreach ($sets['header']['contactus'] as $k => $contact)
                    @php
                        if ($k == 'time') {
                            $icon = 'fas fa-clock';
                        }
                        if ($k == 'location') {
                            $icon = 'fas fa-map-marker-alt';
                        }
                    @endphp
                    <li class="one_quarter">
                        <div class="block clear">
                            <a href="#"><i class="{{ $icon }}"></i></a>
                            <span style="color: #fff;"><strong>{{ $contact['display_name'] }}</strong>{{ $contact['value'] }}</span>
                        </div>
                    </li>
                @endforeach

            </ul>
        </section>
    </div>
@endsection

@section('main')
    <div class="wrapper row3">
        <main class="hoc container clear">
            @if ($sets['header'] && $sets['header']['video'])
            <section id="video" style="margin: 2rem;" >
                <div class="wrapper" style="text-align:center;">
                    {!! $sets['header']['video']->value !!}
                </div>
            </section>
            @endif
            <!-- main body -->
            <section id="services">
                <div class="sectiontitle">
                    <h6 class="heading">{{ __('home.nav.goals') }}</h6>
                    <p class="nospace font-xs">{{ __('home.goalsSubtitle') }}</p>
                </div>
                <ul class="nospace group grid-3">
                    @foreach ($sets['home']['goals'] as $goal)
                        <li class="one_third">

                            <article><a href="{{ route('site.page', 'our-goals') }}"><i
                                        class="{{ $goal['details'] }}"></i></a>
                                <h6 class="heading">{{ $goal['display_name'] }}</h6>
                                <p>{{ $goal['value'] }}</p>
                                <footer><a {{ route('site.page', 'our-goals') }}>{{ __('home.details') }} &raquo;</a>
                                </footer>
                            </article>
                        </li>
                    @endforeach
                </ul>
            </section>


            <!-- / main body -->
            <div class="clear"></div>
        </main>
    </div>

    <div class="bgded overlay"
        style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url({{ asset('storage/' . setting('site.joinImage')) }}) fixed;">
        <section class="hoc container clear">
            <!-- ################################################################################################ -->
            <div class="sectiontitle">
                <p class="nospace font-xs">{{ __('home.joinus.paragraph') }}</p>
                <h6 class="heading">{{ __('home.joinus.title') }}</h6>
            </div>
            <article id="points" class="group">
                @include('components.joinform')
                <div class="one_third last"><img src="{{ asset('storage/' . setting('site.joinFrontImage')) }}"
                        style="border-radius: 1rem;" alt="Join Us now image"></div>
            </article>
            <!-- ################################################################################################ -->
        </section>
    </div>

    @if ($posts && $posts->count() > 0)

        <div class="wrapper row2">
            <section class="hoc container clear">
                <!-- ################################################################################################ -->
                <div class="sectiontitle">
                    <h6 class="heading">{{ __('home.news.title') }}</h6>
                    <p class="nospace font-xs">{{ __('home.news.subtitle') }}</p>
                </div>
                <ul id="latest" class="nospace sd-third">
                    @foreach ($posts as $post)
                        <li>
                            <article>
                                <figure><a class="imgover" href="{{ route('news.post', $post->id) }}"><img
                                            src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}"></a>
                                    <figcaption>
                                        <ul class="nospace meta clear">
                                            <li>
                                                <time
                                                    datetime="2045-04-06T08:15+00:00">{{ date_format($post->created_at, 'Y/m/d H:i:s') }}</time>
                                            </li>
                                        </ul>
                                        <h6 class="heading"><a
                                                href="{{ route('news.post', $post->id) }}">{{ $post->getTranslatedAttribute('title') }}</a>
                                        </h6>
                                    </figcaption>
                                </figure>
                                <p>{{ $post->getTranslatedAttribute('desc') }}</p>
                            </article>
                        </li>
                    @endforeach

                </ul>
                <!-- ################################################################################################ -->
            </section>
        </div>
    @endif
@endsection
