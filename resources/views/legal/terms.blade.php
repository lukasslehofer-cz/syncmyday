@extends('layouts.app')

@section('title', __('messages.terms_of_service') . ' - SyncMyDay')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-10">
        <div class="prose prose-indigo max-w-none">
            @includeFirst(['legal.terms.' . app()->getLocale(), 'legal.terms.en'])
        </div>
    </div>
</div>
@endsection


