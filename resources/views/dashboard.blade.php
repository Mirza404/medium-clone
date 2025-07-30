<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            <div class=" overflow-hidden shadow-sm sm:rounded-lg">
                <ul
                    class="flex flex-wrap text-sm font-medium text-center bg-white text-gray-500 dark:text-gray-400 p-4 justify-center">
                    @foreach ($categories as $category)
                        <li class="me-2">
                            <a href="#"
                                class="inline-block px-4 py-3 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white">{{ $category->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class=" overflow-hidden shadow-sm sm:rounded-lg">
                <ul
                    class="flex flex-wrap text-sm font-medium bg-white text-center text-gray-500 dark:text-gray-400 p-4 justify-center mt-8">
                    @foreach ($posts as $post)
                        <li class="me-2">
                            <a href="#"
                                class="inline-block px-4 py-3 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white">{{ $post->title }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
