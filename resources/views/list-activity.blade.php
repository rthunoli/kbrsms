<x-app-layout>
    <x-slot name="title">
        {{ config('app.name').' - Activities' }} 
    </x-slot>
    
    <x-slot name="other_scripts">
        <script src="{{ asset('js/datepicker.js') }}"></script>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-2xl font-sans text-blue-700 w-fit p-1 mb-4 shadow-md">
                        Activities
                    </div>

                    <button class="mt-4 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                        <a href="{{ route('activities.create') }}">New Activity</a>
                    </button>
                    
                    <div class="font-sans text-gray-600 w-2/3 m-auto border-2 p-4 overflow-hidden shadow-sm rounded-lg">
                        <div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>