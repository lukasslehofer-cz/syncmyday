@extends('layouts.public')

@section('title', __('messages.share_feedback'))

@section('content')
<style>
/* Custom select styling to match sync-rules/create */
.custom-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236366f1'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1.5em 1.5em;
    padding-right: 3rem;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.custom-select option {
    font-family: inherit;
    font-weight: normal;
}
</style>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">{{ __('messages.share_feedback') }}</h1>
        <p class="text-lg text-gray-600">{{ __('messages.feedback_description') }}</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start">
        <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start">
        <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
        <!-- User Info Display (read-only) -->
        <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-xl">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-indigo-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-indigo-900 mb-1">{{ __('messages.feedback_from') }}</p>
                    <p class="text-sm text-indigo-700">{{ $userName }}</p>
                    <p class="text-xs text-indigo-600">{{ $userEmail }}</p>
                </div>
            </div>
        </div>
        
        <form action="{{ route('feedback.send', ['email' => $userEmail, 'name' => $userName, 'signature' => request()->query('signature'), 'expires' => request()->query('expires')]) }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="reason" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('messages.feedback_reason_label') }}</label>
                <select id="reason" name="reason" required
                    class="custom-select w-full px-4 py-3 border-2 border-indigo-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white transition shadow-sm hover:border-indigo-300 @error('reason') border-red-300 @enderror">
                    <option value="">{{ __('messages.feedback_reason_select') }}</option>
                    <option value="not_using" {{ old('reason') === 'not_using' ? 'selected' : '' }}>{{ __('messages.feedback_reason_not_using') }}</option>
                    <option value="too_expensive" {{ old('reason') === 'too_expensive' ? 'selected' : '' }}>{{ __('messages.feedback_reason_too_expensive') }}</option>
                    <option value="missing_features" {{ old('reason') === 'missing_features' ? 'selected' : '' }}>{{ __('messages.feedback_reason_missing_features') }}</option>
                    <option value="technical_issues" {{ old('reason') === 'technical_issues' ? 'selected' : '' }}>{{ __('messages.feedback_reason_technical_issues') }}</option>
                    <option value="found_alternative" {{ old('reason') === 'found_alternative' ? 'selected' : '' }}>{{ __('messages.feedback_reason_found_alternative') }}</option>
                    <option value="other" {{ old('reason') === 'other' ? 'selected' : '' }}>{{ __('messages.feedback_reason_other') }}</option>
                </select>
                @error('reason')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                    {{ __('messages.feedback_additional_comments') }}
                    <span class="text-gray-500 font-normal">({{ __('messages.optional') }})</span>
                </label>
                <textarea id="message" name="message" rows="6"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('message') border-red-300 @enderror"
                    placeholder="{{ __('messages.feedback_placeholder') }}">{{ old('message') }}</textarea>
                @error('message')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:opacity-90 shadow-md transition">
                {{ __('messages.send_feedback') }}
            </button>
        </form>
    </div>

    <!-- Thank You Note -->
    <div class="mt-8 p-6 bg-indigo-50 border border-indigo-200 rounded-xl">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-indigo-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-indigo-900 mb-2">{{ __('messages.feedback_thank_you_title') }}</h3>
                <p class="text-sm text-indigo-700">{{ __('messages.feedback_thank_you_text') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

