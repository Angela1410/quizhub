<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $quiz->title }}</h2>
            <a href="{{ route('instructor.quizzes.index') }}" class="text-sm text-gray-600 hover:underline">&larr; All quizzes</a>
        </div>
    </x-slot>

    @php $field = 'mt-1 block w-full border-gray-300 rounded-md shadow-sm'; @endphp

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="p-3 rounded bg-red-100 text-red-800">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Quiz summary and status actions --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap justify-between gap-4">
                    <div class="text-sm text-gray-700 space-y-1">
                        <p><strong>Status:</strong> {{ ucfirst($quiz->status) }}</p>
                        <p><strong>Schedule:</strong>
                            {{ $quiz->start_at?->format('M d, Y h:i A') ?? 'No start' }} &rarr;
                            {{ $quiz->end_at?->format('M d, Y h:i A') ?? 'No end' }}</p>
                        <p><strong>Time limit:</strong> {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' minutes' : 'None' }}</p>
                        <p><strong>Passing score:</strong> {{ rtrim(rtrim($quiz->passing_score, '0'), '.') }}%
                           &middot; <strong>Max attempts:</strong> {{ $quiz->max_attempts }}</p>
                        <p><strong>Total points:</strong> {{ $quiz->questions->sum('points') }}</p>
                    </div>

                    <div class="flex flex-wrap items-start gap-2">
                        <a href="{{ route('instructor.quizzes.edit', $quiz) }}"
                           class="px-3 py-2 border rounded-md text-sm">Edit settings</a>

                        @foreach ([
                            'draft' => ['published' => 'Publish', 'archived' => 'Archive'],
                            'published' => ['draft' => 'Unpublish', 'archived' => 'Archive'],
                            'archived' => ['draft' => 'Restore to draft'],
                        ][$quiz->status] as $newStatus => $label)
                            <form method="POST" action="{{ route('instructor.quizzes.status', $quiz) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $newStatus }}">
                                <button class="px-3 py-2 rounded-md text-sm text-white {{ $newStatus === 'published' ? 'bg-green-600' : 'bg-gray-700' }}">
                                    {{ $label }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Existing questions --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg mb-4">Questions ({{ $quiz->questions->count() }})</h3>

                @forelse ($quiz->questions as $question)
                    <div class="border rounded-md p-4 mb-3">
                        <div class="flex justify-between gap-4">
                            <p class="font-medium">
                                {{ $loop->iteration }}. {{ $question->question_text }}
                                <span class="text-xs text-gray-500">
                                    ({{ $question->type === 'true_false' ? 'True/False' : 'Multiple choice' }},
                                    {{ $question->points }} {{ Str::plural('pt', $question->points) }})
                                </span>
                            </p>
                            <form method="POST" action="{{ route('instructor.questions.destroy', [$quiz, $question]) }}"
                                  onsubmit="return confirm('Delete this question?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 text-sm hover:underline">Delete</button>
                            </form>
                        </div>
                        <ul class="mt-2 text-sm space-y-1">
                            @foreach ($question->options as $option)
                                <li class="{{ $option->is_correct ? 'text-green-700 font-semibold' : 'text-gray-700' }}">
                                    {{ $option->is_correct ? '✓' : '•' }} {{ $option->option_text }}
                                </li>
                            @endforeach
                        </ul>
                        @if ($question->feedback)
                            <p class="mt-2 text-xs text-gray-500">Feedback: {{ $question->feedback }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No questions yet. Add your first question below.</p>
                @endforelse
            </div>

            {{-- Add a question --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6" x-data="{ type: '{{ old('type', 'multiple_choice') }}' }">
                <h3 class="font-semibold text-lg mb-4">Add a question</h3>

                <form method="POST" action="{{ route('instructor.questions.store', $quiz) }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="type" value="Question type" />
                            <select id="type" name="type" x-model="type" class="{{ $field }}">
                                <option value="multiple_choice">Multiple choice</option>
                                <option value="true_false">True / False</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="points" value="Points" />
                            <input id="points" name="points" type="number" min="1" max="100" class="{{ $field }}"
                                   value="{{ old('points', 1) }}" required>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="question_text" value="Question" />
                        <textarea id="question_text" name="question_text" rows="2" class="{{ $field }}" required>{{ old('question_text') }}</textarea>
                    </div>

                    <div x-show="type === 'multiple_choice'" class="space-y-2">
                        <x-input-label value="Answer choices (select the correct one)" />
                        @foreach (['A', 'B', 'C', 'D'] as $i => $label)
                            <div class="flex items-center gap-2">
                                <input type="radio" name="correct" value="{{ $i }}"
                                       @checked((string) old('correct') === (string) $i)>
                                <input type="text" name="options[{{ $i }}]" value="{{ old('options.' . $i) }}"
                                       placeholder="Choice {{ $label }}" class="block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        @endforeach
                        <p class="text-xs text-gray-500">Leave unused choices blank (minimum of 2).</p>
                    </div>

                    <div x-show="type === 'true_false'" class="space-y-2">
                        <x-input-label value="Correct answer" />
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="tf_answer" value="true" @checked(old('tf_answer') === 'true')> True
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="tf_answer" value="false" @checked(old('tf_answer') === 'false')> False
                        </label>
                    </div>

                    <div>
                        <x-input-label for="feedback" value="Feedback shown to students (optional)" />
                        <textarea id="feedback" name="feedback" rows="2" class="{{ $field }}">{{ old('feedback') }}</textarea>
                    </div>

                    <x-primary-button>Add question</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
