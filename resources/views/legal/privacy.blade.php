@extends('layouts.legal')

@section('title', __('messages.privacy_policy') . ' - SyncMyDay')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 sm:p-12 lg:p-16">
        <div class="legal-content">
            @includeFirst(['legal.privacy.' . app()->getLocale(), 'legal.privacy.en'])
        </div>
    </div>
</div>
@endsection


