@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503 |')
@section('message', __('Service Unavailable'))

@section('content')
    <div class="mt-4 text-lg text-gray-500 uppercase tracking-wider">
        To continue, please visit the <a href="https://engage.exceltrend.com" class="text-info" style="color: rgb(55, 175, 255);">main system here</a>.
    </div>
@endsection