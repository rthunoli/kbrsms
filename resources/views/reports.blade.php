<x-app-layout>
    <x-slot name="title">
        {{ config('app.name').' - Reports' }} 
    </x-slot>
    
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-2xl font-sans text-blue-700 w-fit p-1 mb-4 shadow-md">
                        Reports
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex-auto p-2 border-2 h-screen">
                            <div class="mb-2 p-2 text-blue-600 font-semibold text-center shadow-md">Fee Reports</div>
                            <a href="{{ route('fee_paid') }}" class="block capitalize text-blue-500 hover:font-semibold">fee paid report</a>
                            <a href="{{ route('fee_paid_headwise') }}" class="block capitalize text-blue-500 hover:font-semibold">fee paid headwise report</a>
                        </div>
                        <div class="flex-auto p-2 border-2 h-screen">
                            <div class="mb-2 p-2 text-blue-600 font-semibold text-center shadow-md">Transportation Reports</div>
                            
                            <a href="{{ route('transport_users') }}" class="block capitalize text-blue-500 hover:font-semibold">transport Users list</a>

                            <a href="{{ route('transport_unpaid') }}" class="block capitalize text-blue-500 hover:font-semibold">transport fee unpaid list</a>

                            <a href="{{ route('transport_paid') }}" class="block capitalize text-blue-500 hover:font-semibold">transport fee paid list</a>
                        </div>
                        <div class="flex-auto p-2 border-2 h-screen">
                            <div class="mb-2 p-2 text-blue-600 font-semibold text-center shadow-md">Miscellaneous Reports</div>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>