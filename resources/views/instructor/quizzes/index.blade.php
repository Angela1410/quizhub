<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Quizzes</h2>
            <a href="{{ route('instructor.quizzes.create') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">New Quiz</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Questions</th>
                            <th class="px-4 py-3">Schedule</th>
                            <th class="px-4 py-3">Time limit</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($quizzes as $quiz)
                            @php
                                $badge = [
                                    'draft' => 'bg-gray-100 text-gray-700',
                                    'published' => 'bg-green-100 text-green-800',
                                    'archived' => 'bg-yellow-100 text-yellow-800',
                                ][$quiz->status];
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $quiz->title }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs {{ $badge }}">{{ ucfirst($quiz->status) }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $quiz->questions_count }}</td>
                                <td class="px-4 py-3">
                                    {{ $quiz->start_at?->format('M d, Y h:i A') ?? 'No start' }}
                                    &rarr;
                                    {{ $quiz->end_at?->format('M d, Y h:i A') ?? 'No end' }}
                                </td>
                                <td class="px-4 py-3">{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' min' : 'None' }}</td>
                                <td class="px-4 py-3 space-x-2 whitespace-nowrap">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('instructor.quizzes.show', $quiz) }}">Manage</a>
                                    <a class="text-indigo-600 hover:underline" href="{{ route('instructor.quizzes.edit', $quiz) }}">Edit</a>
                                    <form class="inline" method="POST" action="{{ route('instructor.quizzes.destroy', $quiz) }}"
                                          onsubmit="return confirm('Delete this quiz?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No quizzes yet. Click "New Quiz" to create one.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $quizzes->links() }}</div>
        </div>
    </div>
</x-app-layout>
