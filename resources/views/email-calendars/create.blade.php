@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('email-calendars.index') }}" class="text-sm text-blue-600 hover:text-blue-700 mb-2 inline-block">
            ← Back to Email Calendars
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Add Email Calendar</h1>
        <p class="mt-2 text-sm text-gray-600">
            Create a unique email address to receive calendar invitations
        </p>
    </div>

    <!-- How it works -->
    <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-3">📧 How it works:</h3>
        <ol class="space-y-2 text-sm text-blue-800">
            <li class="flex items-start">
                <span class="font-bold mr-2">1.</span>
                <span>We'll generate a unique email address for you (e.g., <code class="bg-blue-100 px-1 rounded">abc123@{{ config('app.email_domain', 'syncmyday.com') }}</code>)</span>
            </li>
            <li class="flex items-start">
                <span class="font-bold mr-2">2.</span>
                <span>Set up forwarding in your calendar (Outlook, Exchange, etc.) to forward invitations to this address</span>
            </li>
            <li class="flex items-start">
                <span class="font-bold mr-2">3.</span>
                <span>Create sync rules to define where blockers should be created</span>
            </li>
            <li class="flex items-start">
                <span class="font-bold mr-2">4.</span>
                <span>You can also use this calendar as a <strong>target</strong> - send iMIP invitations to any email address</span>
            </li>
        </ol>
    </div>

    <!-- Form -->
    <form action="{{ route('email-calendars.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @csrf

        <!-- Name -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Calendar Name <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="{{ old('name') }}" 
                required
                placeholder="e.g., Work Calendar, Client Meetings" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Give this calendar a descriptive name</p>
        </div>

        <!-- Target Email (optional - for sending iMIP invitations when used as target) -->
        <div class="mb-6">
            <label for="target_email" class="block text-sm font-medium text-gray-700 mb-2">
                Target Email Address (optional)
            </label>
            <input 
                type="email" 
                name="target_email" 
                id="target_email" 
                value="{{ old('target_email') }}" 
                placeholder="e.g., calendar@company.com" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
            @error('target_email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">
                If you plan to use this email calendar as a <strong>target</strong> in sync rules, specify where iMIP invitations should be sent.
            </p>
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description (optional)
            </label>
            <textarea 
                name="description" 
                id="description" 
                rows="3"
                placeholder="e.g., Forwards from my company Outlook calendar"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Sender Whitelist -->
        <div class="mb-6">
            <label for="sender_whitelist" class="block text-sm font-medium text-gray-700 mb-2">
                Allowed Senders (optional)
            </label>
            <textarea 
                name="sender_whitelist" 
                id="sender_whitelist" 
                rows="4"
                placeholder="user@company.com&#10;*@company.com&#10;&#10;Leave empty to allow all senders"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
            >{{ old('sender_whitelist') }}</textarea>
            @error('sender_whitelist')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">
                One email per line. Use <code class="bg-gray-100 px-1 rounded">*@domain.com</code> for wildcards.
                Leave empty to accept from anyone.
            </p>
        </div>

        <!-- Info box -->
        <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h4 class="font-medium text-gray-900 mb-2">💡 Next Steps</h4>
            <p class="text-sm text-gray-600">
                After creating this email calendar, go to <strong>Sync Rules</strong> to define how it should sync with your other calendars.
                You can use it as a <strong>source</strong> (receive forwarded emails) or as a <strong>target</strong> (send iMIP invitations).
            </p>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('email-calendars.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                Create Email Calendar
            </button>
        </div>
    </form>
</div>
@endsection
