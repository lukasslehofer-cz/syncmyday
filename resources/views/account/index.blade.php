@extends('layouts.app')

@section('title', __('messages.account_settings') . ' - SyncMyDay')

@section('content')
<style>
/* Custom select styling */
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

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">Account Settings</h1>
        <p class="text-lg text-gray-600">Manage your account information and preferences</p>
    </div>

    <!-- Account Information -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-6">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">Account Information</h2>
        </div>
        <div class="p-6 lg:p-8">
            @if($user->isOAuthUser())
            <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center shadow-md mr-3">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-blue-800">Signed in with {{ $user->getOAuthProviderName() }}</p>
                        <p class="text-xs text-blue-600">{{ $user->oauth_provider_email }}</p>
                    </div>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('account.update-info') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-bold text-gray-900 mb-2">{{ __('messages.full_name') }}</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name', $user->name) }}" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    >
                    @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-gray-900 mb-2">{{ __('messages.email_address') }}</label>
                    <input 
                        type="email" 
                        value="{{ $user->email }}" 
                        disabled
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-500 cursor-not-allowed"
                    >
                    <p class="mt-2 text-xs text-gray-500">{{ __('messages.email_cannot_be_changed') }}</p>
                </div>

                <div>
                    <label for="timezone" class="block text-sm font-bold text-gray-900 mb-2">{{ __('messages.timezone') }}</label>
                    <select 
                        name="timezone" 
                        id="timezone" 
                        required
                        class="custom-select w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    >
                        @foreach($timezones as $tz)
                        <option value="{{ $tz }}" {{ old('timezone', $user->timezone) === $tz ? 'selected' : '' }}>
                            {{ $tz }}
                        </option>
                        @endforeach
                    </select>
                    @error('timezone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="locale" class="block text-sm font-bold text-gray-900 mb-2">Language</label>
                    <select 
                        name="locale" 
                        id="locale" 
                        required
                        class="custom-select w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    >
                        @foreach($locales as $code => $name)
                        <option value="{{ $code }}" {{ old('locale', $user->locale) === $code ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                        @endforeach
                    </select>
                    @error('locale')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-4">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Password (for OAuth users without password) -->
    @if($user->isOAuthUser() && !$user->password)
    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-xl border-2 border-amber-200 mb-6">
        <div class="px-6 py-5 border-b border-amber-200">
            <h2 class="text-xl font-bold text-gray-900">Add Backup Password</h2>
        </div>
        <div class="p-6 lg:p-8">
            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 mb-1">Recommended: Add a backup login method</p>
                        <p class="text-sm text-gray-600">You currently login with {{ $user->getOAuthProviderName() }}. Add a password so you can also login with email if needed.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('account.add-password') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="add_password" class="block text-sm font-bold text-gray-900 mb-2">New Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="add_password" 
                        required
                        class="w-full px-4 py-3 border-2 border-amber-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition"
                    >
                    <p class="mt-2 text-xs text-gray-500">Minimum 8 characters</p>
                    @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="add_password_confirmation" class="block text-sm font-bold text-gray-900 mb-2">Confirm Password</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="add_password_confirmation" 
                        required
                        class="w-full px-4 py-3 border-2 border-amber-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition"
                    >
                </div>

                <div class="flex justify-end pt-4">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Add Password
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Change Password (for users with password) -->
    @if($user->password)
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-6">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">Change Password</h2>
        </div>
        <div class="p-6 lg:p-8">
            <form method="POST" action="{{ route('account.update-password') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-bold text-gray-900 mb-2">Current Password</label>
                    <input 
                        type="password" 
                        name="current_password" 
                        id="current_password" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    >
                    @error('current_password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-gray-900 mb-2">New Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    >
                    <p class="mt-2 text-xs text-gray-500">Minimum 8 characters</p>
                    @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-gray-900 mb-2">Confirm New Password</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    >
                </div>

                <div class="flex justify-end pt-4">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Connected Login Methods -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-6">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">Login Methods</h2>
        </div>
        <div class="p-6 lg:p-8">
            <p class="text-sm text-gray-600 mb-6">Manage how you can login to your account</p>
            
            <div class="space-y-4">
                <!-- Email/Password -->
                <div class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full {{ $user->password ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                            <svg class="w-5 h-5 {{ $user->password ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Email & Password</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    @if($user->password)
                    <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Active
                    </span>
                    @else
                    <span class="text-xs text-gray-400">Not set up</span>
                    @endif
                </div>

                <!-- Google OAuth -->
                <div class="flex items-center justify-between p-4 border-2 {{ $user->oauth_provider === 'google' ? 'border-blue-200 bg-blue-50' : 'border-gray-200' }} rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full {{ $user->oauth_provider === 'google' ? 'bg-blue-100' : 'bg-gray-100' }} flex items-center justify-center">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="{{ $user->oauth_provider === 'google' ? '#4285F4' : '#9CA3AF' }}" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="{{ $user->oauth_provider === 'google' ? '#34A853' : '#9CA3AF' }}" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="{{ $user->oauth_provider === 'google' ? '#FBBC05' : '#9CA3AF' }}" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="{{ $user->oauth_provider === 'google' ? '#EA4335' : '#9CA3AF' }}" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Google</p>
                            @if($user->oauth_provider === 'google')
                            <p class="text-xs text-gray-500">{{ $user->oauth_provider_email }}</p>
                            @else
                            <p class="text-xs text-gray-500">Not connected</p>
                            @endif
                        </div>
                    </div>
                    @if($user->oauth_provider === 'google')
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Connected
                            </span>
                            @if($user->password)
                            <form method="POST" action="{{ route('account.disconnect-oauth') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">Disconnect</button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400" title="Add a password first to enable disconnect">Can't disconnect</span>
                            @endif
                        </div>
                    @elseif(!$user->oauth_provider)
                        <a href="{{ route('account.connect.google') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Connect</a>
                    @else
                        <span class="text-xs text-gray-400">Connect {{ $user->getOAuthProviderName() }} first</span>
                    @endif
                </div>

                <!-- Microsoft OAuth -->
                <div class="flex items-center justify-between p-4 border-2 {{ $user->oauth_provider === 'microsoft' ? 'border-blue-200 bg-blue-50' : 'border-gray-200' }} rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full {{ $user->oauth_provider === 'microsoft' ? 'bg-blue-100' : 'bg-gray-100' }} flex items-center justify-center">
                            <svg class="w-5 h-5" viewBox="0 0 23 23">
                                <path fill="{{ $user->oauth_provider === 'microsoft' ? '#f35325' : '#D1D5DB' }}" d="M1 1h10v10H1z"/>
                                <path fill="{{ $user->oauth_provider === 'microsoft' ? '#81bc06' : '#D1D5DB' }}" d="M12 1h10v10H12z"/>
                                <path fill="{{ $user->oauth_provider === 'microsoft' ? '#05a6f0' : '#D1D5DB' }}" d="M1 12h10v10H1z"/>
                                <path fill="{{ $user->oauth_provider === 'microsoft' ? '#ffba08' : '#D1D5DB' }}" d="M12 12h10v10H12z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Microsoft</p>
                            @if($user->oauth_provider === 'microsoft')
                            <p class="text-xs text-gray-500">{{ $user->oauth_provider_email }}</p>
                            @else
                            <p class="text-xs text-gray-500">Not connected</p>
                            @endif
                        </div>
                    </div>
                    @if($user->oauth_provider === 'microsoft')
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Connected
                            </span>
                            @if($user->password)
                            <form method="POST" action="{{ route('account.disconnect-oauth') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">Disconnect</button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400" title="Add a password first to enable disconnect">Can't disconnect</span>
                            @endif
                        </div>
                    @elseif(!$user->oauth_provider)
                        <a href="{{ route('account.connect.microsoft') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Connect</a>
                    @else
                        <span class="text-xs text-gray-400">Disconnect {{ $user->getOAuthProviderName() }} first</span>
                    @endif
                </div>
            </div>

            <!-- Info for OAuth users without password -->
            @if($user->oauth_provider && !$user->password)
            <div class="mt-4 bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Want to switch OAuth provider?</p>
                        <p class="text-xs text-blue-700 mt-1">Add a backup password first, then you can disconnect {{ $user->getOAuthProviderName() }} and connect a different provider.</p>
                    </div>
                </div>
            </div>
            @endif

            @if(!$user->password && !$user->isOAuthUser())
            <div class="mt-6 bg-amber-50 border-2 border-amber-200 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-gray-700">You should add at least one login method to secure your account.</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Delete Account -->
    <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-2xl shadow-xl border-2 border-red-200 mb-6">
        <div class="px-6 py-5 border-b border-red-200">
            <h2 class="text-xl font-bold text-red-900">Delete Account</h2>
        </div>
        <div class="p-6 lg:p-8">
            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 mb-1">This action cannot be undone</p>
                        <p class="text-sm text-gray-600">Once you delete your account, all of your data will be permanently deleted including all calendar connections, sync rules, and settings.</p>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('account.destroy') }}" onsubmit="return confirm('Are you absolutely sure you want to delete your account? This cannot be undone!');">
                @csrf
                @method('DELETE')

                @if(!$user->isOAuthUser())
                <div class="mb-6">
                    <label for="delete_password" class="block text-sm font-bold text-gray-900 mb-2">Confirm your password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="delete_password" 
                        required
                        class="w-full px-4 py-3 border-2 border-red-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                        placeholder="Enter your password to confirm"
                    >
                    @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 shadow-lg transform hover:scale-105 transition"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete My Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
