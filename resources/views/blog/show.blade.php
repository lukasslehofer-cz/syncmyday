@extends('layouts.public')

@section('title', $article->getMetaTitle())

@section('meta_description', $article->getMetaDescription())

@section('head')
<!-- Open Graph / Social Media Meta Tags -->
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $article->getMetaTitle() }}">
<meta property="og:description" content="{{ $article->getMetaDescription() }}">
<meta property="og:url" content="{{ url()->current() }}">
@if($article->featured_image)
<meta property="og:image" content="{{ $article->getFeaturedImageUrl() }}">
@endif
<meta property="og:site_name" content="SyncMyDay">

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $article->getMetaTitle() }}">
<meta name="twitter:description" content="{{ $article->getMetaDescription() }}">
@if($article->featured_image)
<meta name="twitter:image" content="{{ $article->getFeaturedImageUrl() }}">
@endif

@if($article->published_at)
<meta property="article:published_time" content="{{ $article->published_at->toIso8601String() }}">
@endif
<meta property="article:section" content="{{ $article->category->getName() }}">
@endsection

@section('sidebar')
    @include('blog.partials.sidebar')
@endsection

@section('content')
<article class="help-content">
    <div class="mb-3 sm:mb-4">
        <a href="{{ route('blog.category', $article->category->slug) }}" class="text-indigo-600 hover:text-indigo-700 text-xs sm:text-sm font-medium">
            ← {{ __('messages.back_to') }} {{ $article->category->getName() }}
        </a>
    </div>

    <header class="mb-6 sm:mb-8">
        <div class="flex items-center mb-3 sm:mb-4">
            <span class="inline-block px-2 sm:px-3 py-1 text-xs sm:text-sm font-semibold text-indigo-600 bg-indigo-100 rounded-full">
                {{ $article->category->getName() }}
            </span>
            @if($article->published_at)
            <span class="ml-3 sm:ml-4 text-xs sm:text-sm text-gray-500">
                {{ $article->published_at->format('d.m.Y') }}
            </span>
            @endif
        </div>

        <h1 class="mb-3 sm:mb-4">{{ $article->getTitle() }}</h1>

        @if($article->getExcerpt())
        <p class="text-lg sm:text-xl text-gray-600">{{ $article->getExcerpt() }}</p>
        @endif
    </header>

    @if($article->featured_image)
    <div class="mb-6 sm:mb-8 rounded-xl overflow-hidden">
        <img src="{{ $article->getFeaturedImageUrl() }}" alt="{{ $article->getTitle() }}" class="w-full">
    </div>
    @endif

    <div class="prose prose-lg max-w-none">
        {!! $article->getContent() !!}
    </div>

    <!-- CTA Block -->
    <div class="my-8 sm:my-10 p-5 sm:p-6 lg:p-8 bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl border border-indigo-200">
        <div class="max-w-2xl mx-auto text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-lg mb-3 sm:mb-4 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-gray-900 mb-2 sm:mb-3">{{ __('messages.blog_cta_title') }}</h2>
            <p class="text-base sm:text-lg mb-5 sm:mb-6 text-gray-700">{{ __('messages.blog_cta_subtitle') }}</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-4 text-base sm:text-lg font-semibold rounded-xl hover:opacity-90 shadow-xl transform hover:scale-105 transition" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white !important; text-decoration: none !important;">
                    {{ __('messages.blog_cta_button_primary') }}
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="{{ route('home') }}#features" class="inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-4 text-base sm:text-lg font-semibold bg-white rounded-xl hover:bg-gray-50 border-2 border-indigo-200 shadow-lg transform hover:scale-105 transition" style="color: #374151 !important; text-decoration: none !important;">
                    {{ __('messages.blog_cta_button_secondary') }}
                </a>
            </div>
            <p class="mt-3 sm:mt-4 text-xs text-gray-600 font-medium">{{ __('messages.blog_cta_footnote') }}</p>
        </div>
    </div>

    @if($relatedArticles->count() > 0)
    <div class="mt-10 sm:mt-12 pt-6 sm:pt-8 border-t border-gray-200">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-5 sm:mb-6">{{ __('messages.blog_related_articles') }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            @foreach($relatedArticles as $related)
            <a href="{{ route('blog.show', $related->slug) }}" class="group bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-xl overflow-hidden hover:shadow-lg transition">
                @if($related->featured_image)
                <div class="h-28 sm:h-32 overflow-hidden">
                    <img src="{{ $related->getFeaturedImageUrl() }}" alt="{{ $related->getTitle() }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                @endif
                <div class="p-3 sm:p-4">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition">
                        {{ $related->getTitle() }}
                    </h3>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</article>
@endsection

