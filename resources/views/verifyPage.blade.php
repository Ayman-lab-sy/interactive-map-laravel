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

            <h4>{{__('home.verify.title')}}</h4>
            <p>
                {{__('home.verify.paragraph')}}
            </p>
           <form action="{{route('verify.post', ['member_id'=>$user_id])}}" method="POST">
                @csrf
                <div class="flexform">
                    <label for="validation_code">{{ __('form.validation_code') }} *</label>
                    <input style="width: auto;" type="text" name="validation_code" value="" required value="{{old('validation_code')}}"/>
                    <button type="submit" class="btn">{{__('form.submit')}}</button>
                    @if ($errors->has('validation_code'))
                        <span class="text-danger">{{ $errors->first('validation_code') }}</span>
                    @endif
                </div>
            </form>

            <!-- / main body -->
        </main>
    </div>
@endsection
