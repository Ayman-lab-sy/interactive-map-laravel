@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .panel-actions .voyager-trash {
            cursor: pointer;
        }

        .panel-actions .voyager-trash:hover {
            color: #e94542;
        }

        .settings .panel-actions {
            right: 0px;
        }

        .panel hr {
            margin-bottom: 10px;
        }

        .panel {
            padding-bottom: 15px;
        }

        .sort-icons {
            font-size: 21px;
            color: #ccc;
            position: relative;
            cursor: pointer;
        }

        .sort-icons:hover {
            color: #37474F;
        }

        .voyager-sort-desc {
            margin-right: 10px;
        }

        .voyager-sort-asc {
            top: 10px;
        }

        .page-title {
            margin-bottom: 0;
        }

        .panel-title code {
            border-radius: 30px;
            padding: 5px 10px;
            font-size: 11px;
            border: 0;
            position: relative;
            top: -2px;
        }

        .modal-open .settings .select2-container {
            z-index: 9 !important;
            width: 100% !important;
        }

        .new-setting {
            text-align: center;
            width: 100%;
            margin-top: 20px;
        }

        .new-setting .panel-title {
            margin: 0 auto;
            display: inline-block;
            color: #999fac;
            font-weight: lighter;
            font-size: 13px;
            background: #fff;
            width: auto;
            height: auto;
            position: relative;
            padding-right: 15px;
        }

        .settings .panel-title {
            padding-left: 0px;
            padding-right: 0px;
        }

        .new-setting hr {
            margin-bottom: 0;
            position: absolute;
            top: 7px;
            width: 96%;
            margin-left: 2%;
        }

        .new-setting .panel-title i {
            position: relative;
            top: 2px;
        }

        .new-settings-options {
            display: none;
            padding-bottom: 10px;
        }

        .new-settings-options label {
            margin-top: 13px;
        }

        .new-settings-options .alert {
            margin-bottom: 0;
        }

        #toggle_options {
            clear: both;
            float: right;
            font-size: 12px;
            position: relative;
            margin-top: 15px;
            margin-right: 5px;
            margin-bottom: 10px;
            cursor: pointer;
            z-index: 9;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .new-setting-btn {
            margin-right: 15px;
            position: relative;
            margin-bottom: 0;
            top: 5px;
        }

        .new-setting-btn i {
            position: relative;
            top: 2px;
        }

        textarea {
            min-height: 120px;
        }

        textarea.hidden {
            display: none;
        }

        .voyager .settings .nav-tabs {
            background: none;
            border-bottom: 0px;
        }

        .voyager .settings .nav-tabs .active a {
            border: 0px;
        }

        .select2 {
            width: 100% !important;
            border: 1px solid #f1f1f1;
            border-radius: 3px;
        }

        .voyager .settings input[type=file] {
            width: 100%;
        }

        .settings .select2 {
            margin-left: 10px;
        }

        .settings .select2-selection {
            height: 32px;
            padding: 2px;
        }

        .voyager .settings .nav-tabs>li {
            margin-bottom: -1px !important;
        }

        .voyager .settings .nav-tabs a {
            text-align: center;
            background: #f8f8f8;
            border: 1px solid #f1f1f1;
            position: relative;
            top: -1px;
            border-bottom-left-radius: 0px;
            border-bottom-right-radius: 0px;
        }

        .voyager .settings .nav-tabs a i {
            display: block;
            font-size: 22px;
        }

        .tab-content {
            background: #ffffff;
            border: 1px solid transparent;
        }

        .tab-content>div {
            padding: 10px;
        }

        .settings .no-padding-left-right {
            padding-left: 0px;
            padding-right: 0px;
        }

        .nav-tabs>li.active>a,
        .nav-tabs>li.active>a:focus,
        .nav-tabs>li.active>a:hover {
            background: #fff !important;
            color: #62a8ea !important;
            border-bottom: 1px solid #fff !important;
            top: -1px !important;
        }

        .nav-tabs>li a {
            transition: all 0.3s ease;
        }


        .nav-tabs>li.active>a:focus {
            top: 0px !important;
        }

        .voyager .settings .nav-tabs>li>a:hover {
            background-color: #fff !important;
        }
    </style>
@stop

@section('page_title', __('voyager::generic.edit') . ' ' . $dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.edit') . ' ' . $dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    {{-- @include('voyager::multilingual.language-selector') --}}
@stop

@section('content')
    <div class="page-content settings container-fluid">
        <div class="row">
            <div class="col-md-12">

                <!-- form start -->
                <form id="frm_settings" action="{{ route('siteSettings.updateAll') }}" method="POST">
                    <input type="hidden" name="lang" value="{{request()->get('lang') ?? 'en'}}" />
                    <div class="panel">

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif


                            <div class="page-content settings container-fluid">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a data-toggle="tab" href="#home_content">Home page</a>
                                    </li>
                                    <li>
                                        <a data-toggle="tab" href="#donation_page">Donation Page</a>
                                    </li>
                                    <li>
                                        <a data-toggle="tab" href="#joinus_page">Join us Page</a>
                                    </li>
                                    <li>
                                        <a data-toggle="tab" href="#news_page">News Page</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div id="home_content" class="tab-pane fade in active">
                                        <div class="panel-body no-padding-left-right">
                                            <div>

                                                <div class="row">
                                                    <div class="col-md-2 no-padding-left-right">
                                                        <label
                                                            for="{{$settings['site']['title']->config_key}}">{{ $settings['site']['title']->display_name }}</label>
                                                    </div>
                                                    <div class="col-md-10 no-padding-left-right">
                                                            <input name="{{ $settings['site']['title']->config_key }}"
                                                                id="{{ $settings['site']['title']->config_key }}" type="text"
                                                                class="form-control" required
                                                                value="{{ $settings['site']['title']->value }}" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <h3 style="margin-bottom: 1rem">Header Section</h3>
                                                @foreach ($settings['home']['heading'] as $set)
                                                    <div class="row">
                                                        <div class="col-md-2 no-padding-left-right">
                                                            <label
                                                                for="{{ $set->config_key }}">{{ $set->display_name }}</label>
                                                        </div>
                                                        <div class="col-md-10 no-padding-left-right">
                                                            @if (str_contains($set->config_key, 'paragraph'))
                                                                <textarea class="form-control" required name="{{ $set->config_key }}" id="{{ $set->config_key }}">{{ $set->value }}</textarea>
                                                            @else
                                                                <input name="{{ $set->config_key }}"
                                                                    id="{{ $set->config_key }}" type="text"
                                                                    class="form-control" required
                                                                    value="{{ $set->value }}" />
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <div class="row">
                                                    <div class="col-md-2 no-padding-left-right">
                                                        <label id="lbl.header.contactus.time">Header Wokring Hours</label>
                                                    </div>
                                                    <div class="col-md-10 no-padding-left-right">
                                                        <div class="col-md-6">
                                                            <input name="header.contactus.time-display_name" type="text"
                                                                aria-labelledby="lbl.header.contactus.time"
                                                                placeholder="Days" class="form-control" required
                                                                value="{{ $settings['header']['contactus']['time']->display_name }}" />
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input name="header.contactus.time-value" type="text"
                                                                aria-labelledby="lbl.header.contactus.time"
                                                                class="form-control" placeholder="Time" required
                                                                value="{{ $settings['header']['contactus']['time']->value }}" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-2 no-padding-left-right">
                                                        <label id="lbl.header.contactus.location">Header Address</label>
                                                    </div>
                                                    <div class="col-md-10 no-padding-left-right">
                                                        <textarea class="form-control" required name="{{ $settings['header']['contactus']['location']->config_key }}" id="{{ $settings['header']['contactus']['location']->config_key }}">{{ $settings['header']['contactus']['location']->value }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-2 no-padding-left-right">
                                                        <label
                                                            for="{{ $settings['header']['video']->config_key }}">{{ $settings['header']['video']->display_name }}</label>
                                                    </div>
                                                    <div class="col-md-10 no-padding-left-right">
                                                       @include('formfields.tinymce_alone', [
                                                        'vitem' => $settings['header']['video'],
                                                       ])
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <h3 style="margin-bottom: 1rem">Goals Section</h3>
                                                @foreach ($settings['home']['goals'] as $set)
                                                    <div class="row">
                                                        <div class="col-md-2 no-padding-left-right">
                                                            <label
                                                                for="{{ $set->config_key }}">{{ $set->display_name }}</label>
                                                        </div>
                                                        <div class="col-md-10 no-padding-left-right">
                                                            <textarea class="form-control" required name="{{ $set->config_key }}" id="{{ $set->config_key }}">{{ $set->value }}</textarea>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div id="donation_page" class="tab-pane fade in">
                                        <div class="panel-body no-padding-left-right">
                                            <div>
                                                @foreach ($settings['donate'] as $key => $set)
                                                    <div class="row">
                                                        <div class="col-md-2 no-padding-left-right">
                                                            <label class="block"
                                                                for="{{ $set->config_key }}">{{ $set->display_name }}</label>
                                                            @if ($key === 'paragraph')
                                                                <small class="block">Enter a paragraph to be displayed
                                                                    before the donation button like Bank
                                                                    Information....etc</small>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-10 no-padding-left-right">
                                                            @if ($key === 'paragraph' || $key === 'subtitle')
                                                                <textarea class="form-control" required name="{{ $set->config_key }}" id="{{ $set->config_key }}">{{ $set->value }}</textarea>
                                                            @else
                                                                <input name="{{ $set->config_key }}"
                                                                    id="{{ $set->config_key }}" type="text"
                                                                    class="form-control" required
                                                                    value="{{ $set->value }}" />
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>
                                    </div>
                                    <div id="joinus_page" class="tab-pane fade in">
                                        <div class="panel-body no-padding-left-right">
                                            <div>
                                                @foreach ($settings['join'] as $key => $set)
                                                    <div class="row">
                                                        <div class="col-md-2 no-padding-left-right">
                                                            <label
                                                                for="{{ $set->config_key }}">{{ $set->display_name }}</label>
                                                        </div>
                                                        <div class="col-md-10 no-padding-left-right">
                                                            @if ($key === 'paragraph' || $key === 'subtitle')
                                                                <textarea class="form-control" required name="{{ $set->config_key }}" id="{{ $set->config_key }}">{{ $set->value }}</textarea>
                                                            @else
                                                                <input name="{{ $set->config_key }}"
                                                                    id="{{ $set->config_key }}" type="text"
                                                                    class="form-control" required
                                                                    value="{{ $set->value }}" />
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>
                                    </div>
                                    <div id="news_page" class="tab-pane fade in">
                                        <div class="panel-body no-padding-left-right">
                                            <div>
                                                @foreach ($settings['news'] as $key => $set)
                                                    <div class="row">
                                                        <div class="col-md-2 no-padding-left-right">
                                                            <label
                                                                for="{{ $set->config_key }}">{{ $set->display_name }}</label>
                                                        </div>
                                                        <div class="col-md-10 no-padding-left-right">
                                                            @if ($key === 'paragraph' || $key === 'subtitle')
                                                                <textarea class="form-control" required name="{{ $set->config_key }}" id="{{ $set->config_key }}">{{ $set->value }}</textarea>
                                                            @else
                                                                <input name="{{ $set->config_key }}"
                                                                    id="{{ $set->config_key }}" type="text"
                                                                    class="form-control" required
                                                                    value="{{ $set->value }}" />
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>


                        </div><!-- panel-body -->

                    </div>

                </form>
                <button type="submit" onclick="submitForm()" class="btn btn-primary save" >Save</button>
            </div>
        </div>
    </div>


@stop

@section('javascript')
    <script>
        const submitForm = () => {
            let frm = document.getElementById('frm_settings')
            frm.submit()
        }
        $('document').ready(function() {
            $('[data-toggle="tab"]').click(function() {
                $(".setting_tab").val($(this).html());
            });

        });
    </script>
@stop
