@extends('layouts.app')

@section('title', 'Sent Email - Admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.emails') }}" class="text-sm text-indigo-600 hover:text-indigo-700">← Zpět na odeslané e-maily</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <dl class="grid sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Předmět</dt>
                <dd class="font-semibold text-gray-900">{{ $sentEmail->subject ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Odesláno</dt>
                <dd class="text-gray-900">{{ $sentEmail->sent_at?->translatedFormat('M d, Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Od</dt>
                <dd class="text-gray-900">{{ $sentEmail->from_email ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Komu</dt>
                <dd class="text-gray-900">{{ $sentEmail->to_all ?? $sentEmail->to_email }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Mailer</dt>
                <dd class="text-gray-900">{{ $sentEmail->mailer ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Stav</dt>
                <dd class="text-gray-900">{{ $sentEmail->status }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-200 bg-gray-50">
            <h2 class="text-sm font-medium text-gray-700">Náhled</h2>
        </div>
        @if($sentEmail->html_body)
            <iframe srcdoc="{{ $sentEmail->html_body }}"
                    sandbox=""
                    class="w-full"
                    style="height: 70vh; border: 0;"></iframe>
        @elseif($sentEmail->text_body)
            <pre class="p-6 text-sm text-gray-800 whitespace-pre-wrap">{{ $sentEmail->text_body }}</pre>
        @else
            <p class="p-6 text-sm text-gray-500">Tělo e-mailu není k dispozici.</p>
        @endif
    </div>
</div>
@endsection
