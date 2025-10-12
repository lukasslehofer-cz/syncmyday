@extends('layouts.app')

@section('title', 'Upravit článek')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('admin.blog.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium mb-4 inline-block">
            ← Zpět na seznam článků
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Upravit článek</h1>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.blog.update', $article->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Základní informace</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategorie *</label>
                    <select name="category_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $article->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->getName() }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug (URL) *</label>
                    <input type="text" name="slug" value="{{ $article->slug }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Pouze malá písmena, čísla a pomlčky</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hlavní obrázek (URL)</label>
                    <input type="text" name="featured_image" value="{{ $article->featured_image }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_published" value="1" id="is_published" {{ $article->is_published ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_published" class="ml-2 block text-sm text-gray-900">Publikovat</label>
                </div>
            </div>
        </div>

        <!-- Translations -->
        @foreach($locales as $locale)
        @php
            $translation = $article->translations->where('locale', $locale)->first();
        @endphp
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                {{ strtoupper($locale) }} verze
                @if($locale === 'cs')<span class="text-red-600">*</span>@endif
            </h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titulek {{ $locale === 'cs' ? '*' : '' }}</label>
                    <input type="text" name="title_{{ $locale }}" value="{{ $translation?->title }}" {{ $locale === 'cs' ? 'required' : '' }} class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Výtah (krátký popis)</label>
                    <textarea name="excerpt_{{ $locale }}" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ $translation?->excerpt }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Obsah (HTML) {{ $locale === 'cs' ? '*' : '' }}</label>
                    <textarea name="content_{{ $locale }}" {{ $locale === 'cs' ? 'required' : '' }} rows="15" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm">{{ $translation?->content }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title (SEO)</label>
                    <input type="text" name="meta_title_{{ $locale }}" value="{{ $translation?->meta_title }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description (SEO)</label>
                    <textarea name="meta_description_{{ $locale }}" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ $translation?->meta_description }}</textarea>
                </div>
            </div>
        </div>
        @endforeach

        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.blog.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Zrušit
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Uložit změny
            </button>
        </div>
    </form>
</div>
@endsection

