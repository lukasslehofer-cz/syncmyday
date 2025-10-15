@extends('layouts.app')

@section('title', __('messages.edit_calendar'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-4">
            <a href="{{ route('connections.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                {{ __('messages.edit_calendar') }}
            </h1>
        </div>
        <p class="text-lg text-gray-600">
            {{ __('messages.edit_calendar_description') }}
        </p>
    </div>

    <!-- Form -->
    <form action="{{ route('connections.update', $connection) }}" method="POST" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        @csrf
        @method('PUT')

        <div class="p-6 lg:p-8 space-y-6">
            <!-- Provider Info (read-only) -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-4">
                <div class="flex items-center space-x-3">
                    @if($connection->provider === 'google')
                        <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                        </div>
                    @elseif($connection->provider === 'microsoft')
                        <div class="h-12 w-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
                            </svg>
                        </div>
                    @elseif($connection->provider === 'caldav')
                        <div class="h-12 w-12 bg-gradient-to-r from-gray-800 to-black rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <p class="font-bold text-gray-900">
                            @if($connection->provider === 'caldav')
                                {{ __('messages.caldav_calendar') }}
                            @else
                                {{ ucfirst($connection->provider) }} {{ __('messages.calendar_singular') }}
                            @endif
                        </p>
                        <p class="text-sm text-gray-600 font-medium">{{ $connection->account_email ?? $connection->provider_email }}</p>
                    </div>
                </div>
            </div>

            <!-- Calendar Name -->
            <div>
                <label for="name" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    <span>{{ __('messages.calendar_name') }} <span class="text-red-500">*</span></span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name', $connection->name) }}" 
                    required
                    placeholder="{{ __('messages.calendar_name_placeholder') }}" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-medium transition"
                >
                @error('name')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-500">{{ __('messages.calendar_name_hint') }}</p>
            </div>

            <!-- Calendar Selection -->
            @if($connection->available_calendars && count($connection->available_calendars) > 0)
            <div>
                <label class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-4">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ __('messages.select_calendar_to_use') }} <span class="text-red-500">*</span></span>
                </label>
                
                @if(count($connection->available_calendars) === 1)
                    <!-- Only one calendar - show as read-only -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-xl px-4 py-3">
                        <div class="flex items-center space-x-3">
                            @if($connection->available_calendars[0]['primary'] ?? false)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                                    {{ __('messages.primary') }}
                                </span>
                            @endif
                            <span class="font-medium text-gray-900">{{ $connection->available_calendars[0]['name'] }}</span>
                        </div>
                    </div>
                    <input type="hidden" name="selected_calendar_id" value="{{ $connection->available_calendars[0]['id'] }}">
                @else
                    <!-- Multiple calendars - show as cards -->
                    <div class="space-y-3">
                        @foreach($connection->available_calendars as $index => $calendar)
                            <label class="calendar-option flex items-center p-4 border-2 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 transition cursor-pointer group {{ (old('selected_calendar_id', $selectedCalendarId) === $calendar['id']) ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200' }}">
                                <input 
                                    type="radio" 
                                    name="selected_calendar_id" 
                                    value="{{ $calendar['id'] }}"
                                    class="calendar-radio w-5 h-5 text-indigo-600 border-2 border-gray-300 focus:ring-indigo-500 focus:ring-2"
                                    {{ (old('selected_calendar_id', $selectedCalendarId) === $calendar['id']) ? 'checked' : '' }}
                                    required
                                    onchange="updateCalendarSelection()"
                                >
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center space-x-3">
                                        @if($calendar['primary'] ?? false)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                                                {{ __('messages.primary') }}
                                            </span>
                                        @endif
                                        <span class="font-semibold text-gray-900 group-hover:text-indigo-700">
                                            {{ $calendar['name'] }}
                                        </span>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </label>
                        @endforeach
                    </div>

                    <script>
                    function updateCalendarSelection() {
                        // Remove highlight from all options
                        document.querySelectorAll('.calendar-option').forEach(label => {
                            label.classList.remove('border-indigo-400', 'bg-indigo-50');
                            label.classList.add('border-gray-200');
                        });
                        
                        // Add highlight to selected option
                        const checkedRadio = document.querySelector('.calendar-radio:checked');
                        if (checkedRadio) {
                            const label = checkedRadio.closest('.calendar-option');
                            label.classList.remove('border-gray-200');
                            label.classList.add('border-indigo-400', 'bg-indigo-50');
                        }
                    }
                    </script>

                    @error('selected_calendar_id')
                        <p class="mt-4 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">{{ __('messages.select_calendar_hint') }}</p>
                @endif
            </div>
            @endif
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('connections.index') }}" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-100 transition">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('messages.save_changes') }}
            </button>
        </div>
    </form>

    <!-- Delete Calendar -->
    <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-2xl shadow-xl border-2 border-red-200 mt-8">
        <div class="px-6 py-5 border-b border-red-200">
            <h2 class="text-xl font-bold text-red-900">{{ __('messages.delete_calendar') }}</h2>
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
                        <p class="text-sm font-bold text-gray-900 mb-1">{{ __('messages.action_cannot_be_undone') }}</p>
                        <p class="text-sm text-gray-600">{{ __('messages.delete_calendar_warning') }}</p>
                        <ul class="text-sm text-gray-600 mt-2 ml-4 list-disc">
                            <li>{{ __('messages.delete_calendar_warning_1') }}</li>
                            <li>{{ __('messages.delete_calendar_warning_2') }}</li>
                            <li>{{ __('messages.delete_calendar_warning_3') }}</li>
                            @if($connection->provider !== 'caldav')
                            <li>{{ __('messages.delete_calendar_warning_4') }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            
            @if($connection->provider === 'caldav')
            <form action="{{ route('caldav.disconnect', $connection) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_calendar_confirm') }}')">
                @csrf
                @method('DELETE')
                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 shadow-lg transform hover:scale-105 transition"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ __('messages.delete_this_calendar') }}
                    </button>
                </div>
            </form>
            @else
            <form action="{{ route('connections.destroy', $connection) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_calendar_confirm') }}')">
                @csrf
                @method('DELETE')
                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 shadow-lg transform hover:scale-105 transition"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ __('messages.delete_this_calendar') }}
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

