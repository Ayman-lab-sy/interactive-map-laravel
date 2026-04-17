@extends('components.layout')

@section('headerImg', 'url(' . asset('storage/' . setting('site.joinusPage')) . ');')
@section('header')
    <div class="row inspace-80" style="padding-top: 10rem;">
        <h2 class="font-x3">{{$set['title']['value']}}</h2>
        <h3 class="font-x1">{{$set['subtitle']['value']}}</h3>
    </div>
@endsection
@section('main')
    <div class="wrapper row3">
        <main class="hoc container clear">
            <!-- main body -->
            @if ($verified)
                <h1 style="text-align: center;">{{__('home.verified')}}</h1>
            @else
                {!! $set['paragraph']['value'] !!}

                @include('components.joinform')
            @endif

            <!-- / main body -->
        </main>
    </div>
@endsection
