<div>
    <ul class="flex flex-wrap text-sm font-medium text-center bg-white text-gray-500 dark:text-gray-400 justify-center">
        <li class="me-2">
            <a href="#" class="inline-block px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                All
            </a>
        </li>
        @foreach ($categories as $category)
            <li class="me-2">
                <a href="#"
                    class="inline-block px-4 py-2 rounded-lg hover:text-gray-900 hover:bg-gray-100 ">{{ $category->name }}</a>
            </li>
        @endforeach
    </ul>
</div>
