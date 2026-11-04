<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Quiz</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('instructor.quizzes.update', $quiz) }}">
                    @csrf
                    @method('PUT')
                    @include('instructor.quizzes._form')
                    <div class="mt-6 flex gap-3">
                        <x-primary-button>Save changes</x-primary-button>
                        <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="px-4 py-2 text-sm text-gray-600">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
