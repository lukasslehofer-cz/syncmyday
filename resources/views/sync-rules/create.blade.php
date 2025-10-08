@extends('layouts.app')

@section('title', 'Create Sync Rule')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm-px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('messages.create_sync_rule') }}</h1>
        <p class="mt-2 text-gray-600">{{ __('messages.create_sync_rule_description') }}</p>
    </div>
    
    <form action="{{ route('sync-rules.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        
        <!-- Source Calendar -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.source_calendar') }}</label>
            <p class="text-sm text-gray-500 mb-3">{{ __('messages.source_calendar_description') }}</p>
            
            <select id="source_connection_select" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">{{ __('messages.select_calendar') }}...</option>
                
                @if($apiConnections->count() > 0)
                <optgroup label="{{ __('messages.api_calendars') }}">
                    @foreach($apiConnections as $connection)
                    <option value="api-{{ $connection->id }}" 
                            data-type="api"
                            data-connection-id="{{ $connection->id }}"
                            data-calendars='@json($connection->available_calendars)'>
                        {{ ucfirst($connection->provider) }} - {{ $connection->provider_email }}
                    </option>
                    @endforeach
                </optgroup>
                @endif
                
                @if($emailConnections->count() > 0)
                <optgroup label="{{ __('messages.email_calendars') }}">
                    @foreach($emailConnections as $connection)
                    <option value="email-{{ $connection->id }}"
                            data-type="email"
                            data-connection-id="{{ $connection->id }}">
                        📧 {{ $connection->name }} ({{ $connection->email_address }})
                    </option>
                    @endforeach
                </optgroup>
                @endif
            </select>
            
            <input type="hidden" name="source_type" id="source_type">
            <input type="hidden" name="source_connection_id" id="source_connection_id">
            <input type="hidden" name="source_email_connection_id" id="source_email_connection_id">
            
            <select name="source_calendar_id" id="source_calendar" required class="mt-3 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" disabled>
                <option value="">{{ __('messages.first_select_connection') }}...</option>
            </select>
        </div>
        
        <!-- Target Calendars -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.target_calendars') }}</label>
            <p class="text-sm text-gray-500 mb-3">{{ __('messages.target_calendars_description') }}</p>
            
            <div id="targets-container" class="space-y-3">
                <!-- Target rows will be added here dynamically -->
            </div>
            
            <button type="button" id="add-target" class="mt-3 text-sm text-indigo-600 hover:text-indigo-700">
                + {{ __('messages.add_target') }}
            </button>
        </div>
        
        <!-- Blocker Title -->
        <div>
            <label for="blocker_title" class="block text-sm font-medium text-gray-700">{{ __('messages.blocker_title') }}</label>
            <input type="text" name="blocker_title" id="blocker_title" value="Busy — Sync" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <p class="mt-1 text-sm text-gray-500">{{ __('messages.blocker_title_description') }}</p>
        </div>
        
        <!-- Direction -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.sync_direction') }}</label>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="direction" value="one_way" checked class="h-4 w-4 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">{{ __('messages.one_way') }}</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="direction" value="two_way" class="h-4 w-4 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">{{ __('messages.two_way') }}</span>
                </label>
            </div>
        </div>
        
        <!-- Filters -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.filters') }}</label>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="filters[busy_only]" value="1" checked class="h-4 w-4 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">{{ __('messages.only_busy_events') }}</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="filters[ignore_all_day]" value="1" class="h-4 w-4 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">{{ __('messages.ignore_all_day') }}</span>
                </label>
            </div>
        </div>
        
        <div class="flex justify-end space-x-3">
            <a href="{{ route('sync-rules.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                {{ __('messages.create_rule') }}
            </button>
        </div>
    </form>
</div>

<script>
let targetIndex = 0;

const apiConnections = @json($apiConnections);
const emailConnections = @json($emailConnections);

// Source calendar selection logic
document.getElementById('source_connection_select').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const type = selectedOption.dataset.type;
    const connectionId = selectedOption.dataset.connectionId;
    
    document.getElementById('source_type').value = type || '';
    
    if (type === 'api') {
        // Show calendar selector for API calendars
        const calendars = JSON.parse(selectedOption.dataset.calendars || '[]');
        const calendarSelect = document.getElementById('source_calendar');
        calendarSelect.innerHTML = `<option value="">{{ __('messages.select_calendar') }}...</option>`;
        calendars.forEach(cal => {
            calendarSelect.innerHTML += `<option value="${cal.id}">${cal.name}</option>`;
        });
        calendarSelect.disabled = false;
        calendarSelect.required = true;
        calendarSelect.style.display = 'block';
        
        document.getElementById('source_connection_id').value = connectionId;
        document.getElementById('source_email_connection_id').value = '';
        
    } else if (type === 'email') {
        // Email calendars don't have sub-calendars - hide the calendar selector
        const calendarSelect = document.getElementById('source_calendar');
        calendarSelect.disabled = true;
        calendarSelect.required = false;
        calendarSelect.style.display = 'none';
        
        document.getElementById('source_connection_id').value = '';
        document.getElementById('source_email_connection_id').value = connectionId;
    }
});

// Add first target automatically
addTargetRow();

// Add target button
document.getElementById('add-target').addEventListener('click', addTargetRow);

function addTargetRow() {
    const container = document.getElementById('targets-container');
    const index = targetIndex++;
    
    const div = document.createElement('div');
    div.className = 'target-row border border-gray-200 rounded-md p-3 space-y-2';
    div.innerHTML = `
        <select class="target-connection-select w-full px-3 py-2 border border-gray-300 rounded-md" required>
            <option value="">{{ __('messages.select_calendar') }}...</option>
            ${apiConnections.length > 0 ? `<optgroup label="{{ __('messages.api_calendars') }}">
                ${apiConnections.map(conn => `
                    <option value="api-${conn.id}" 
                            data-type="api"
                            data-connection-id="${conn.id}"
                            data-calendars='${JSON.stringify(conn.available_calendars)}'>
                        ${conn.provider.toUpperCase()} - ${conn.provider_email}
                    </option>
                `).join('')}
            </optgroup>` : ''}
            ${emailConnections.length > 0 ? `<optgroup label="{{ __('messages.email_calendars') }}">
                ${emailConnections.map(conn => `
                    <option value="email-${conn.id}"
                            data-type="email"
                            data-connection-id="${conn.id}"
                            data-target-email="${conn.target_email || 'No target email configured'}">
                        📧 ${conn.name} (${conn.email_address})
                    </option>
                `).join('')}
            </optgroup>` : ''}
        </select>
        
        <input type="hidden" name="target_connections[${index}][type]" class="target-type">
        <input type="hidden" name="target_connections[${index}][connection_id]" class="target-connection-id">
        <input type="hidden" name="target_connections[${index}][email_connection_id]" class="target-email-connection-id">
        
        <select name="target_connections[${index}][calendar_id]" class="target-calendar w-full px-3 py-2 border border-gray-300 rounded-md" disabled>
            <option value="">{{ __('messages.select_calendar') }}...</option>
        </select>
        
        ${index > 0 ? `<button type="button" class="remove-target text-sm text-red-600 hover:text-red-700">{{ __('messages.remove') }}</button>` : ''}
    `;
    
    container.appendChild(div);
    
    // Attach event listeners
    const connectionSelect = div.querySelector('.target-connection-select');
    connectionSelect.addEventListener('change', function() {
        handleTargetChange(this);
    });
    
    if (index > 0) {
        div.querySelector('.remove-target').addEventListener('click', function() {
            div.remove();
        });
    }
}

function handleTargetChange(select) {
    const row = select.closest('.target-row');
    const selectedOption = select.options[select.selectedIndex];
    const type = selectedOption.dataset.type;
    const connectionId = selectedOption.dataset.connectionId;
    
    row.querySelector('.target-type').value = type || '';
    
    if (type === 'api') {
        // Show calendar selector for API calendars
        const calendars = JSON.parse(selectedOption.dataset.calendars || '[]');
        const calendarSelect = row.querySelector('.target-calendar');
        calendarSelect.innerHTML = `<option value="">{{ __('messages.select_calendar') }}...</option>`;
        calendars.forEach(cal => {
            calendarSelect.innerHTML += `<option value="${cal.id}">${cal.name}</option>`;
        });
        calendarSelect.disabled = false;
        calendarSelect.required = true;
        calendarSelect.style.display = 'block';
        
        row.querySelector('.target-connection-id').value = connectionId;
        row.querySelector('.target-email-connection-id').value = '';
        
    } else if (type === 'email') {
        // Email calendars - show target email in the calendar selector
        const targetEmail = selectedOption.dataset.targetEmail || 'No target email configured';
        const calendarSelect = row.querySelector('.target-calendar');
        calendarSelect.innerHTML = `<option value="" selected>${targetEmail}</option>`;
        calendarSelect.disabled = true;
        calendarSelect.required = false;
        calendarSelect.style.display = 'block';
        
        row.querySelector('.target-connection-id').value = '';
        row.querySelector('.target-email-connection-id').value = connectionId;
    }
}
</script>
@endsection
