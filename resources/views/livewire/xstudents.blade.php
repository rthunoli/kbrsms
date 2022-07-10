<x-app-layout>
    <x-slot name="title">
        {{ config('app.name').' - Search' }} 
    </x-slot>

    <x-slot name="livewire_styles">
        @livewireStyles
    </x-slot>

    <x-slot name="livewire_scripts">
        @livewireScripts
    </x-slot>
    {{-- {{ dd($students) }} --}}
    <div class="py-2">
        <div class="mx-auto sm:px-1 lg:px-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-2xl font-sans text-blue-700 w-fit p-1 mb-4 shadow-md">
                        Students
                    </div>
                    <div class="border sm:rounded-lg overflow-x-auto">
                        <livewire:search-students-table />
                    </div>
                </div>
            </div>
        </div>         
    </div>
</x-app-layout>