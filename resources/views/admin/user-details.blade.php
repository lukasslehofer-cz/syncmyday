@extends('layouts.app')

@section('title', 'User Details - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('admin.users') }}" class="text-indigo-600 hover:text-indigo-700 mb-4 inline-block">← Back to Users</a>
        <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
        <p class="text-gray-600">{{ $user->email }}</p>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold mb-2">Subscription</h3>
            <p class="text-2xl font-bold">{{ ucfirst($user->subscription_tier) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold mb-2">Connections</h3>
            <p class="text-2xl font-bold">{{ $user->calendarConnections->count() }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold mb-2">Sync Rules</h3>
            <p class="text-2xl font-bold">{{ $user->syncRules->count() }}</p>
        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Calendar Connections</h2>
        @if($user->calendarConnections->isEmpty())
        <p class="text-gray-500">No connections</p>
        @else
        <div class="space-y-3">
            @foreach($user->calendarConnections as $connection)
            <div class="border border-gray-200 p-4 rounded-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium">{{ ucfirst($connection->provider) }}</p>
                        <p class="text-sm text-gray-500">{{ $connection->provider_email }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded {{ $connection->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $connection->status }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

