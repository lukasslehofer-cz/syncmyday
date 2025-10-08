@extends('layouts.app')

@section('title', 'Webhooks - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Webhook Subscriptions</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Calendar ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($webhooks as $webhook)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        {{ $webhook->calendarConnection->user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        {{ ucfirst($webhook->calendarConnection->provider) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">
                        {{ Str::limit($webhook->calendar_id, 20) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded {{ $webhook->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $webhook->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $webhook->isExpiringSoon() ? 'text-orange-600 font-medium' : 'text-gray-500' }}">
                        {{ $webhook->expires_at->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $webhooks->links() }}
    </div>
</div>
@endsection

