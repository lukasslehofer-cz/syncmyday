@extends('layouts.app')

@section('title', 'Create First Rule - Onboarding')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Progress -->
    <div class="mb-8">
        <div class="flex items-center justify-between text-sm">
            <span class="text-indigo-600 font-medium">Step 2 of 3</span>
            <span class="text-gray-500">Create sync rule</span>
        </div>
        <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full bg-indigo-600" style="width: 66%"></div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🔄</div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Your First Sync Rule</h1>
            <p class="text-gray-600">Set up how your calendars sync with each other</p>
        </div>

        <form action="{{ route('sync-rules.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Source Calendar -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    📤 Source Calendar
                </label>
                <p class="text-sm text-gray-500 mb-3">Events from this calendar will create blockers</p>
                
                <select name="source_connection_id" id="source_connection" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select calendar...</option>
                    @foreach($connections as $connection)
                    <option value="{{ $connection->id }}" data-calendars='@json($connection->available_calendars)'>
                        {{ ucfirst($connection->provider) }} - {{ $connection->provider_email }}
                    </option>
                    @endforeach
                </select>
                
                <select name="source_calendar_id" id="source_calendar" required class="mt-3 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" disabled>
                    <option value="">First select a connection...</option>
                </select>
            </div>

            <!-- Target Calendar -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    📥 Target Calendar
                </label>
                <p class="text-sm text-gray-500 mb-3">Blockers will be created here</p>
                
                <select name="target_connections[0][connection_id]" class="target-connection w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">Select calendar...</option>
                    @foreach($connections as $connection)
                    <option value="{{ $connection->id }}" data-calendars='@json($connection->available_calendars)'>
                        {{ ucfirst($connection->provider) }} - {{ $connection->provider_email }}
                    </option>
                    @endforeach
                </select>
                
                <select name="target_connections[0][calendar_id]" class="target-calendar mt-3 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required disabled>
                    <option value="">Select calendar...</option>
                </select>
            </div>

            <!-- Blocker Title -->
            <div>
                <label for="blocker_title" class="block text-sm font-medium text-gray-700">
                    Blocker Title
                </label>
                <input type="text" name="blocker_title" id="blocker_title" value="Busy — Sync" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                <p class="mt-1 text-sm text-gray-500">This will appear in your target calendar (no event details)</p>
            </div>

            <!-- Direction -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sync Direction</label>
                <div class="space-y-2">
                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="direction" value="one_way" checked class="h-4 w-4 text-indigo-600">
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">One-way sync →</span>
                            <span class="block text-xs text-gray-500">Source to target only (recommended)</span>
                        </div>
                    </label>
                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="direction" value="two_way" class="h-4 w-4 text-indigo-600">
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">Two-way sync ↔</span>
                            <span class="block text-xs text-gray-500">Bidirectional (advanced)</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Hidden filters -->
            <input type="hidden" name="filters[busy_only]" value="1">
            <input type="hidden" name="filters[ignore_all_day]" value="0">

            <!-- Navigation -->
            <div class="flex justify-between items-center pt-6 border-t">
                <a href="{{ route('onboarding.connect-calendars') }}" class="text-gray-600 hover:text-gray-900">
                    ← Back
                </a>
                
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                    Create Rule & Finish →
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Calendar selection logic (same as create sync rule page)
document.getElementById('source_connection').addEventListener('change', function() {
    const calendars = JSON.parse(this.options[this.selectedIndex].dataset.calendars || '[]');
    const calendarSelect = document.getElementById('source_calendar');
    calendarSelect.innerHTML = '<option value="">Select calendar...</option>';
    calendars.forEach(cal => {
        calendarSelect.innerHTML += `<option value="${cal.id}">${cal.name}</option>`;
    });
    calendarSelect.disabled = false;
});

document.querySelector('.target-connection').addEventListener('change', function() {
    const calendars = JSON.parse(this.options[this.selectedIndex].dataset.calendars || '[]');
    const calendarSelect = document.querySelector('.target-calendar');
    calendarSelect.innerHTML = '<option value="">Select calendar...</option>';
    calendars.forEach(cal => {
        calendarSelect.innerHTML += `<option value="${cal.id}">${cal.name}</option>`;
    });
    calendarSelect.disabled = false;
});
</script>
@endsection

