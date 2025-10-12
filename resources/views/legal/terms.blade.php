@extends('layouts.legal')

@section('title', __('messages.terms_of_service') . ' - SyncMyDay')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 sm:p-12 lg:p-16">
        <div class="legal-content">
            @includeFirst(['legal.terms.' . app()->getLocale(), 'legal.terms.en'])
        </div>
    </div>
</div>
@endsection


