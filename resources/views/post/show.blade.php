<x-app-layout>
    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <h1 class="text-5xl mb-4">{{ $post->title }}</h1>
                <div class="flex gap-4">
                    {{-- User avatar section --}}
                    @if ($post->user->image)
                        <img src="{{ $post->user->imageUrl() }}" alt="{{ $post->user->name }}"
                            class="w-12 h-12 rounded-full">
                    @else
                        <img src="https://www.shutterstock.com/image-vector/vector-design-avatar-dummy-sign-600nw-1290556063.jpg"
                            alt="" class="w-12 h-12 rounded-full">
                    @endif
                    <div>
                        <div class="flex gap-2">
                            <h3>{{ $post->user->name }}</h3>
                            &middot;
                            <a href="#" class="text-emerald-600">Follow</a>
                        </div>
                        <div class="flex gap-2 text-gray-500">
                            {{ $post->readTime() }}min read
                            &middot;
                            {{ $post->created_at->format('d M, Y') }}
                        </div>
                    </div>
                </div>
                {{-- Clap section --}}
                <x-clap-button />
                {{-- User content section --}}
                <div class="mt-8">
                    <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="w-full rounded-lg">

                    <div class="mt-4">
                        {{ $post->content }}
                    </div>
                </div>

                <div class="mt-8 ">
                    <span class="px-4 py-2 bg-gray-300 rounded-2xl">
                        {{ $post->category->name }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
