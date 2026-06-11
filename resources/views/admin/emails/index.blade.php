@extends('layouts.app')

@section('title', 'Sent Emails - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Odeslané e-maily</h1>

        <form method="GET" class="flex space-x-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Příjemce nebo předmět…"
                   class="px-3 py-2 border border-gray-300 rounded-md">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Hledat</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Čas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Příjemce</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Předmět</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mailer</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($emails as $email)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $email->sent_at?->translatedFormat('M d, H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        {{ $email->to_email }}
                        @if($email->to_all)
                            <span class="text-xs text-gray-400">+ další</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ Str::limit($email->subject, 70) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $email->mailer ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        <a href="{{ route('admin.email-details', $email) }}" class="text-indigo-600 hover:text-indigo-700">Náhled</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Žádné odeslané e-maily.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $emails->withQueryString()->links() }}
    </div>
</div>
@endsection
