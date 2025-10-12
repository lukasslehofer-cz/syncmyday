@extends('layouts.public')

@section('title', __('messages.blog_title'))

@section('meta_description', __('messages.blog_meta_description'))

@section('sidebar')
    @include('blog.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
    <h1>{{ __('messages.blog_title') }}</h1>
    
    <p class="text-xl text-gray-600 mb-8">{{ __('messages.blog_subtitle') }}</p>

    @if($articles->count() > 0)
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            @foreach($articles as $article)
            <a href="{{ route('blog.show', $article->slug) }}" class="group bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-xl overflow-hidden hover:shadow-lg transition">
                @if($article->featured_image)
                <div class="h-48 overflow-hidden">
                    <img src="{{ $article->getFeaturedImageUrl() }}" alt="{{ $article->getTitle() }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                @endif
                <div class="p-6">
                    <div class="flex items-center mb-3">
                        <span class="inline-block px-3 py-1 text-xs font-semibold text-indigo-600 bg-indigo-100 rounded-full">
                            {{ $article->category->getName() }}
                        </span>
                        @if($article->published_at)
                        <span class="ml-auto text-sm text-gray-500">
                            {{ $article->published_at->format('d.m.Y') }}
                        </span>
                        @endif
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition mb-2">
                        {{ $article->getTitle() }}
                    </h2>
                    @if($article->getExcerpt())
                    <p class="text-gray-600">{{ $article->getExcerpt() }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        {{ $articles->links() }}
    @else
        <div class="p-8 bg-gray-50 border border-gray-200 rounded-xl text-center">
            <p class="text-gray-600">{{ __('messages.blog_no_articles') }}</p>
        </div>
    @endif
</div>
@endsection

