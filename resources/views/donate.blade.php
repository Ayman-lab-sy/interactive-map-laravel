@extends('components.layout')

@section('headerImg', 'url(' . asset('storage/' . setting('site.donateImage')) . ');')
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

            {!! $set['paragraph']['value'] !!}

            @if (setting('general.paypal_email'))
                @include('components.paypal')
            @endif

            <!-- / main body -->
        </main>
    </div>
@endsection
