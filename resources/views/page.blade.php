@extends('components.layout')

@section('headerImg', 'url(' . asset('storage/' . $page->image) . ');')
@section('header')
    <div class="row inspace-80" style="padding-top: 10rem;">
        <h2 class="font-x3">{{ $page->title }}</h2>
    </div>
@endsection
@section('main')
    <div class="wrapper row3">
        <main class="hoc container clear">
            {!! $page->content !!}
        </main>
    </div>
@endsection
