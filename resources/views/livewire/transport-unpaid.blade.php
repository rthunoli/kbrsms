<x-app-layout>
    <x-slot name="title">
        {{ config('app.name').' - Transport Fee Unpaid' }} 
    </x-slot>

    <x-slot name="livewire_styles">
        @livewireStyles
    </x-slot>
    
    <x-slot name="other_scripts">
        <script src="{{ asset('js/datepicker.js') }}"></script>
    </x-slot>

    <x-slot name="livewire_scripts">
        @livewireScripts
    </x-slot>
    <div class="py-2">
        <div class="mx-auto sm:px-1 lg:px-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-2xl font-sans text-blue-700 w-fit p-1 mb-4 shadow-md">
                        Transport Fee Unpaid List
                    </div>
                    
                    <x-date-range action="{{ route('transport_unpaid') }}" />
                    
                    @if (request()->filled('start_date') && request()->filled('end_date') && !session('invalid_date_range'))
                        <div class="mt-2 border sm:rounded-lg overflow-x-auto">
                            <livewire:transport-unpaid-table />
                        </div>
                    @endif
                </div>
            </div>
        </div>         
    </div>
</x-app-layout>

