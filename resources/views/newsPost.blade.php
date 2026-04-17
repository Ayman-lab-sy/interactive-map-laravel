@extends('components.layout')

@section('headerImg', 'url(' . asset('storage/' . $post->image) . ');')
@section('header')
    <div class="row inspace-80" style="padding-top: 10rem;">
        <h2 class="font-x3">{{ $post->title }}</h2>
        <h3 class="font-x1">{{ $post->desc }}</h3>
    </div>
@endsection
@section('main')
    <div class="wrapper row3">
        <main class="hoc container clear">
            {!! $post->content !!}
        </main>
    </div>
@endsection
