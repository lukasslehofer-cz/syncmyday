@extends('layouts.public')

@section('title', $category->getName() . ' - ' . __('messages.blog_title'))

@section('meta_description', $category->getDescription() ?: __('messages.blog_meta_description'))

@section('sidebar')
    @include('blog.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
    <div class="mb-3 sm:mb-4">
        <a href="{{ route('blog.index') }}" class="text-indigo-600 hover:text-indigo-700 text-xs sm:text-sm font-medium">
            ← {{ __('messages.back_to_blog') }}
        </a>
    </div>

    <h1>{{ $category->getName() }}</h1>
    
    @if($category->getDescription())
    <p class="text-lg sm:text-xl text-gray-600 mb-6 sm:mb-8">{{ $category->getDescription() }}</p>
    @endif

    @if($articles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
            @foreach($articles as $article)
            <a href="{{ route('blog.show', $article->slug) }}" class="group bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-xl overflow-hidden hover:shadow-lg transition">
                @if($article->featured_image)
                <div class="h-40 sm:h-48 overflow-hidden">
                    <img src="{{ $article->getFeaturedImageUrl() }}" alt="{{ $article->getTitle() }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                @endif
                <div class="p-4 sm:p-6">
                    <div class="flex items-center mb-2 sm:mb-3">
                        @if($article->published_at)
                        <span class="text-xs sm:text-sm text-gray-500">
                            {{ $article->published_at->format('d.m.Y') }}
                        </span>
                        @endif
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition mb-1.5 sm:mb-2">
                        {{ $article->getTitle() }}
                    </h2>
                    @if($article->getExcerpt())
                    <p class="text-sm sm:text-base text-gray-600">{{ $article->getExcerpt() }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        {{ $articles->links() }}
    @else
        <div class="p-6 sm:p-8 bg-gray-50 border border-gray-200 rounded-xl text-center">
            <p class="text-sm sm:text-base text-gray-600">{{ __('messages.blog_no_articles_in_category') }}</p>
        </div>
    @endif
</div>
@endsection

