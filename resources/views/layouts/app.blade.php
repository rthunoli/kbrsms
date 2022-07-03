<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
        <title>{{ $title ?? '' }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
        <style>
            [x-cloak] { display: none !important; }
        </style>
        
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>  
        <script src="{{ asset('js/flowbite.js') }}"></script>
        
        {{ $other_scripts ?? '' }}
        {{ $livewire_styles ?? '' }}
        {{ $other_styles ?? '' }}
        {{-- <script src="{{ asset('js/splitter.js') }}"></script> --}}
    </head>
    <body class="font-sans antialiased">
        {{-- "bg-gray-100" --}}
        <div id="main-layout" class="min-h-screen bg-[url('/img/beams.jpg')]">
            <!-- Header -->
            <header>
                @include('layouts.navigation')
            </header>
            
            <!-- Page Heading -->
            {{-- <header class="bg-white shadow col-span-2">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header ?? '' }}
                </div>
            </header> --}}

            <!-- Side bar -->
            <aside class="border-r-2 border-gray-200">
                <button id="selectdb" data-dropdown-toggle="dropdown" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                    @if (session()->has('db'))
                        {{ session('db') }}
                    @else
                        Select DB     
                    @endif
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></button>

                <div id="dropdown" class="hidden z-10 w-44 text-base list-none bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700">
                    <ul class="py-1" aria-labelledby="dropdownButton">
                        <li>
                            <a href="{{ route('selectdb',['db'=>'EMS']) }}" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">EMS</a>
                        </li>
                        <li>
                            <a href="{{ route('selectdb',['db'=>'HSS']) }}" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">HSS</a>
                        </li>
                        
                        {{-- <li>
                            <a href="{{ route('session') }}" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Check Session</a>
                        </li> --}}
                    </ul>
                </div>

                <button id="search" class="mt-4 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                    <a href="{{ route('search') }}">Search Stuents</a>
                </button>

            </aside>
            
            <!-- Page Content -->
            <main class="overflow-x-auto">
                @if (session('dbnotok'))
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800 text-center" role="alert">
                        {{ session('dbnotok')  }}
                    </div>
                @endif
                {{ $slot }}
            </main>
            
            <!-- Footer -->
            <footer class="bg-white" >
                <p class="text-center font-sans text-gray-500 "> 
                    KBR-SMS | Deveoped by Rajendran T K | rajendran.thunoli@gmail.com    
                </p> 
            </footer>
        </div>
        {{ $livewire_scripts ?? '' }}
    </body>
</html>
