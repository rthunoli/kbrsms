<x-app-layout>
    <x-slot name="title">
        {{ config('app.name').' - New Activity' }} 
    </x-slot>
    
    <x-slot name="other_scripts">
        <script src="{{ asset('js/datepicker.js') }}"></script>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-2xl font-sans text-blue-700 w-fit p-1 mb-4 shadow-md">
                        New Activity
                    </div>
                    @if(session('success'))
                        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 
                                 dark:text-green-800 text-center"   role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('activities.store') }}" method="POST">
                        @csrf
                        <div class="font-sans text-gray-600 w-1/2 m-auto border-2 p-4 overflow-hidden shadow-sm rounded-lg">
                            <div class="mb-4">
                                Activity Date
                                @error('activity_date')
                                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800 text-center" role="alert">{{ $message }}</div>
                                @enderror
                                <input datepicker="" datepicker-format="dd/mm/yyyy" name="activity_date" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-5 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date" value="{{ old('activity_date') }}">
                            </div>
                            <div class="mb-4">
                                Activity
                                @error('activity')
                                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800 text-center" role="alert">{{ $message }}</div>
                                @enderror
                                <input type="text" maxlength="50" name="activity" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-5 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Activity" value="{{ old('activity') }}">
                            </div> 
                            <div class="mb-4">
                                Description
                                @error('description')
                                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800 text-center" role="alert">{{ $message }}</div>
                                @enderror
                                <textarea maxlength="1000" rows="10" cols="50" name="description" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-5 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Description">{{ old('description') }}</textarea>
                            </div>
                            <div>
                                <button type="submit" class="text-white right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>