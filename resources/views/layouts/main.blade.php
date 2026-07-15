@extends('adminlte::page')

@section('content_header')
    @yield('header')
@endsection

@section('content')
    @yield('content_body')
@endsection

@section('preloader')
    <div id="preloader">
        <div class="loader">

            <img src="{{ asset('img/logo.png') }}" alt="VENDEX">

            <div class="spinner"></div>

        </div>

        <h3 class="mt-4">VENDEX</h3>
        <p>Cargando sistema...</p>
    </div>
@endsection
