@extends('voyager::master')

@section('precss')
<script src="https://cdn.tailwindcss.com"></script>
@stop
@section('head')
<style>
.flex {border: none !important;}
.panel-body {
    visibility: initial;
}
</style>
@stop

@section('content')
    <div class="page-content">
        @include('voyager::alerts')
        @include('voyager::dimmers')
        <div class="min-h-screen  text-gray-500 py-6 flex flex-col sm:py-2">
            <div class="px-4 w-full lg:px-0 sm:max-w-5xl sm:mx-auto">
                <div class="flex justify-end">
                    @include('analytics::data.filter')
                </div>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @each('analytics::stats.card', $stats, 'stat')
                </div>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @include('analytics::data.pages-card')
                    @include('analytics::data.sources-card')
                    @include('analytics::data.users-card')
                    @include('analytics::data.devices-card')
                    @each('analytics::data.utm-card', $utm, 'data')
                </div>
            </div>
        </div>
    </div>
@stop


@section('javascript')
<script>
    const filterButton = document.getElementById('filter-button');
    const filterDropdown = document.getElementById('filter-dropdown');

    filterButton.addEventListener('click', function (e) {
        e.preventDefault();

        filterDropdown.style.display = 'block';
    });

    document.addEventListener('click', function (e) {
        if (!filterButton.contains(e.target) && !filterDropdown.contains(e.target)) {
            filterDropdown.style.display = 'none';
        }
    });
</script>
@stop
