@extends('layouts.main')

@section('header')
    {{ $header ?? '' }}
@endsection

@section('content_body')
    <livewire:release-notification />
    {{ $slot }}
@endsection
