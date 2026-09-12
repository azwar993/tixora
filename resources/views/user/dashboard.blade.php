<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard TIXORA
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold">
                        Selamat datang, {{ auth()->user()->name }}!
                    </h1>

                    <p class="mt-3">
                        Email: {{ auth()->user()->email }}
                    </p>

                    <p class="mt-2">
                        Role: {{ auth()->user()->role }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>