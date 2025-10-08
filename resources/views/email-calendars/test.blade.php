@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('email-calendars.show', $emailCalendar) }}" class="text-sm text-blue-600 hover:text-blue-700 mb-2 inline-block">
            ← Back to {{ $emailCalendar->name }}
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Test Email Processing</h1>
        <p class="mt-2 text-sm text-gray-600">
            Test your email calendar by pasting a sample email with .ics attachment
        </p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Test Form -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">📧 Paste Test Email</h2>
                
                <form action="{{ route('email-calendars.test.process', $emailCalendar) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email_content" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Content (with headers and .ics attachment)
                        </label>
                        <textarea 
                            name="email_content" 
                            id="email_content" 
                            rows="20"
                            required
                            placeholder="Paste raw email content here..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-xs"
                        >{{ old('email_content') }}</textarea>
                        @error('email_content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                        Process Test Email
                    </button>
                </form>
            </div>
        </div>

        <!-- Help & Example -->
        <div class="space-y-6">
            <!-- How to get test email -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">💡 How to get a test email:</h3>
                <ol class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start">
                        <span class="font-bold mr-2">1.</span>
                        <span>Send yourself a calendar invitation from Outlook/Gmail</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">2.</span>
                        <span>Open the invitation email in your email client</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">3.</span>
                        <span>View source / Show original / Download as .eml</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">4.</span>
                        <span>Copy the entire raw email content and paste it above</span>
                    </li>
                </ol>
            </div>

            <!-- Sample Email -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">📄 Sample Email Template</h3>
                <p class="text-sm text-gray-600 mb-3">Here's a minimal example you can use for testing:</p>
                
                <pre class="bg-white border border-gray-300 rounded p-3 text-xs font-mono overflow-x-auto">From: test@example.com
To: {{ $emailCalendar->email_address }}
Subject: Meeting Invitation
Content-Type: multipart/mixed; boundary="boundary123"

--boundary123
Content-Type: text/plain

You have a meeting invitation.

--boundary123
Content-Type: text/calendar; name="meeting.ics"
Content-Disposition: attachment; filename="meeting.ics"

BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Test//Test//EN
METHOD:REQUEST
BEGIN:VEVENT
UID:test-{{ time() }}@example.com
DTSTAMP:{{ now()->format('Ymd\THis\Z') }}
DTSTART:{{ now()->addDay()->format('Ymd\THis\Z') }}
DTEND:{{ now()->addDay()->addHour()->format('Ymd\THis\Z') }}
SUMMARY:Test Meeting
DESCRIPTION:This is a test meeting
STATUS:CONFIRMED
SEQUENCE:0
END:VEVENT
END:VCALENDAR

--boundary123--</pre>

                <button 
                    onclick="document.getElementById('email_content').value = this.previousElementSibling.textContent; window.scrollTo({top: 0, behavior: 'smooth'})"
                    class="mt-3 px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded"
                >
                    Copy to Form
                </button>
            </div>

            <!-- What happens -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-green-900 mb-3">✨ What happens when you submit:</h3>
                <ul class="space-y-2 text-sm text-green-800">
                    <li class="flex items-start">
                        <span class="mr-2">✓</span>
                        <span>Email is parsed and .ics attachments are extracted</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">✓</span>
                        <span>Events are extracted from .ics files</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">✓</span>
                        <span>Blockers are created in your target calendars based on active sync rules</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">✓</span>
                        <span>You'll see a success message with the number of events processed</span>
                    </li>
                </ul>
            </div>

            <!-- Calendar Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">📊 Calendar Info</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Name:</span>
                        <span class="font-medium">{{ $emailCalendar->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <code class="text-xs bg-gray-100 px-1 rounded">{{ $emailCalendar->email_address }}</code>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium">{{ ucfirst($emailCalendar->status) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Events Processed:</span>
                        <span class="font-medium">{{ $emailCalendar->events_processed }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

