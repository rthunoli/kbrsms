<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot> --}}

    <x-slot name="title">
        {{ config('app.name').' - Home' }} 
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="font-sans text-gray-600">
                        <ul class="flex flex-col m-auto w-9/12 text-sm font-medium text-gray-900 bg-white overflow-hidden rounded-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            
                            <li class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-center bg-blue-500 text-white font-semibold">STEPS</li>
                            <li class="py-2 px-4 rounded-t-lg border-b border-gray-200 dark:border-gray-600">1. Select a database</li>
                            <li class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">2. Search for students</li>

                            <li class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-center bg-blue-500 text-white font-semibold">NOTES</li>
                            <li class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">1. You can search any word or number</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>