<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">


            <div class=" overflow-hidden shadow-sm sm:rounded-lg">
                <ul class="flex flex-wrap text-sm font-medium text-center bg-white text-gray-500 p-4 justify-center">
                    <x-category-tabs class="me-2"></x-category-tabs>
                </ul>
            </div>

            <div class="mt-8 text-gray-900">
                <div>
                    <ul>
                        @forelse ($posts as $post)
                            <li>
                                <x-post-item :post="$post"></x-post-item>
                            </li>
                        @empty
                            <li class="text-center text-gray-500">No posts available.</li>
                        @endforelse
                    </ul>


                </div>
                {{ $posts->links() }}
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
