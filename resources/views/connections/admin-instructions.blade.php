@extends('layouts.app')

@section('title', __('messages.admin_consent_instructions'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('connections.index') }}" class="mr-4 text-gray-600 hover:text-gray-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('messages.admin_consent_instructions') }}</h1>
        </div>
        <p class="text-lg text-gray-600">{{ __('messages.admin_consent_explanation') }}</p>
    </div>

    <!-- Connection Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-lg bg-yellow-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-900">{{ $connection->provider_email }}</p>
                <p class="text-sm text-gray-600">{{ __('messages.waiting_for_it_admin') }}</p>
            </div>
        </div>
    </div>

    <!-- Why Admin Approval -->
    <div class="bg-blue-50 border-l-4 border-blue-400 rounded-lg p-6 mb-8">
        <h3 class="text-lg font-bold text-blue-900 mb-2">
            {{ __('messages.admin_consent_why_needed') }}
        </h3>
        <p class="text-blue-800">
            {{ __('messages.admin_consent_explanation') }}
        </p>
    </div>

    <!-- Steps for IT Admin -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('messages.admin_consent_steps_title') }}</h2>
        
        <div class="space-y-6">
            <!-- Step 1 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">
                    1
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ __('messages.admin_consent_step_1') }}</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-sm text-gray-600 mb-2 font-medium">{{ __('messages.admin_consent_url') }}</p>
                        <div class="flex gap-2">
                            <input type="text" 
                                   id="adminConsentUrl" 
                                   value="{{ $adminConsentUrl }}" 
                                   readonly 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono bg-white">
                            <button onclick="copyAdminConsentUrl()" 
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                                {{ __('messages.admin_consent_copy_url') }}
                            </button>
                        </div>
                        <p id="copySuccess" class="text-sm text-green-600 mt-2 hidden">{{ __('messages.url_copied_to_clipboard') }}</p>
                    </div>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">
                    2
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ __('messages.admin_consent_step_2') }}</h3>
                    <p class="text-gray-600">{{ __('messages.admin_redirect_description') }}</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">
                    3
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ __('messages.admin_consent_step_3') }}</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-sm font-medium text-gray-700 mb-2">{{ __('messages.requested_permissions') }}</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span><strong>Calendars.ReadWrite</strong> - {{ __('messages.permission_calendars') }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span><strong>User.Read</strong> - {{ __('messages.permission_user') }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span><strong>offline_access</strong> - {{ __('messages.permission_offline') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">
                    4
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ __('messages.admin_consent_step_4') }}</h3>
                    <p class="text-gray-600">{{ __('messages.approval_organization_wide') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alternative Solution -->
    <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 mb-8">
        <h3 class="font-bold text-gray-900 mb-2">{{ __('messages.admin_consent_alternative') }}</h3>
        <p class="text-gray-600 mb-4">
            {{ __('messages.personal_accounts_no_consent') }}
        </p>
        <a href="{{ route('connections.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('messages.connect_different_calendar') }}
        </a>
    </div>

    <!-- Contact Admin Template -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('messages.admin_consent_contact_admin') }}</h2>
        <p class="text-gray-600 mb-4">{{ __('messages.use_template_description') }}</p>
        
        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
            <p class="text-sm text-gray-800 whitespace-pre-line">{{ __('messages.admin_email_template', ['url' => $adminConsentUrl]) }}</p>
        </div>
        
        <button onclick="copyEmailTemplate()" 
                class="mt-4 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg transition">
            {{ __('messages.copy_email_template') }}
        </button>
        <p id="copyEmailSuccess" class="text-sm text-green-600 mt-2 hidden">{{ __('messages.template_copied_to_clipboard') }}</p>
    </div>
</div>

<script>
function copyAdminConsentUrl() {
    const url = document.getElementById('adminConsentUrl');
    url.select();
    document.execCommand('copy');
    
    const success = document.getElementById('copySuccess');
    success.classList.remove('hidden');
    setTimeout(() => success.classList.add('hidden'), 3000);
}

function copyEmailTemplate() {
    const template = document.querySelector('.whitespace-pre-line').innerText;
    navigator.clipboard.writeText(template);
    
    const success = document.getElementById('copyEmailSuccess');
    success.classList.remove('hidden');
    setTimeout(() => success.classList.add('hidden'), 3000);
}
</script>
@endsection

