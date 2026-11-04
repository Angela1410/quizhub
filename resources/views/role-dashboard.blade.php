<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                <p>
                    Welcome, {{ auth()->user()->name }}! You are logged in as
                    <strong>{{ ucfirst(auth()->user()->role) }}</strong>.
                </p>

                @if (auth()->user()->isInstructor())
                    <a href="{{ route('instructor.quizzes.index') }}"
                       class="inline-block mt-4 px-4 py-2 bg-gray-800 text-white rounded-md text-sm">
                        Manage my quizzes
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
