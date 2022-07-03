<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('About') }}
        </h2>
    </x-slot> --}}

    <x-slot name='title'>
        {{ config('app.name').' - About' }}
    </x-slot>

    <x-slot name="other_scripts">
        <script src="{{ asset('js/datepicker.js') }}"></script>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-2xl font-sans text-blue-700 w-fit p-1 mb-4 shadow-md">
                        About
                    </div>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis consequatur odit et quae esse.
                    Voluptatum adipisci ipsam recusandae magni, nihil laudantium dicta vero nesciunt, est molestiae
                    laboriosam illum, distinctio earum?


                    {{-- <div x-data="{ open: false }">
                        <button @click="open = !open">Click to List Categories</button>
                        <ul x-show="open">
                            <li>PHP</li>
                            <li>Laravel</li>
                            <li>Vue</li>
                            <li>React</li>
                        </ul>
                    </div> --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>