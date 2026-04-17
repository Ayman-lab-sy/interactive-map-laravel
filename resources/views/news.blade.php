@extends('components.layout')

@section('headerImg', 'url(' . asset('storage/' . setting('site.newsPage')) . ');')
@section('header')
    <div class="row inspace-80" style="padding-top: 10rem;">
        <h2 class="font-x3">{{ $set['title']['value'] }}</h2>
        <h3 class="font-x1">{{ $set['subtitle']['value'] }}</h3>
    </div>
@endsection
@section('main')
    <div class="wrapper row3">
        <main class="hoc container clear">
            <section class="hoc container clear">
            <!-- main body -->
            <ul id="latest" class="nospace  sd-third">
                @foreach ($news as $post)
                <li>
                        <article>
                            <figure><a class="imgover" href="{{route('news.post', $post->id)}}"><img src="{{ asset('storage/' . $post->image) }}"
                                        alt="{{ $post->getTranslatedAttribute('title') }}"></a>
                                <figcaption>
                                    <ul class="nospace meta clear">
                                        <li>
                                            <time
                                                datetime="2045-04-06T08:15+00:00">{{ date_format($post->created_at, 'Y/m/d H:i:s') }}</time>
                                        </li>
                                    </ul>
                                    <h6 class="heading"><a href="{{route('news.post', $post->id)}}">{{ $post->getTranslatedAttribute('title') }}</a>
                                    </h6>
                                </figcaption>
                            </figure>
                            <p>{{ $post->getTranslatedAttribute('desc') }}</p>
                        </article>
                    </li>
                @endforeach

            </ul>
            {{ $news->links('vendor.pagination.default') }}
            </section>
            <!-- / main body -->
        </main>
    </div>
@endsection
