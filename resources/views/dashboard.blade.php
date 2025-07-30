<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            <div class=" overflow-hidden shadow-sm sm:rounded-lg">
                <ul class="flex flex-wrap text-sm font-medium text-center bg-white text-gray-500 p-4 justify-center">
                    @foreach ($categories as $category)
                        <li class="me-2">
                            <a href="#"
                                class="inline-block px-4 py-3 rounded-lg hover:text-gray-900 hover:bg-gray-100 ">{{ $category->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-8 text-gray-900">
                <div>
                    <ul>
                        @foreach ($posts as $post)
                            <div class=" bg-white border border-gray-200 rounded-lg shadow-sm ">
                                <a href="#">
                                    <img class="rounded-t-lg" src="/docs/images/blog/image-1.jpg" alt="" />
                                </a>
                                <div class="p-5">
                                    <a href="#">
                                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">
                                            Noteworthy technology acquisitions 2021</h5>
                                    </a>
                                    <p class="mb-3 font-normal text-gray-700 ">Here are the biggest
                                        enterprise technology acquisitions of 2021 so far, in reverse chronological
                                        order.</p>
                                    <a href="#"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 ">
                                        Read more
                                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </ul>

                    
                </div>
            </div>
        </div>
        {{ $posts->links() }}
    </div>
    </div>
</x-app-layout>
