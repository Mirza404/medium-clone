<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Your Reading List</h1>

        @if($readingList->isEmpty())
            <p class="text-gray-600">Your reading list is empty. Start adding articles to read later!</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($readingList as $article)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <a href="{{ route('reading-lists.show', $article->slug) }}" class="block">
                            <img src="{{ $article->cover_image_url }}" alt="{{ $article->title }}" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h2 class="text-xl font-semibold mb-2">{{ $article->title }}</h2>
                                <p class="text-gray-600 text-sm mb-4">{{ $article->author->name }} · {{ $article->created_at->diffForHumans() }}</p>
                                <p class="text-gray-700">{{ Str::limit($article->excerpt, 100) }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>