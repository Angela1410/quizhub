@php $field = 'mt-1 block w-full border-gray-300 rounded-md shadow-sm'; @endphp

<div class="space-y-5">
    <div>
        <x-input-label for="title" value="Quiz title" />
        <input id="title" name="title" type="text" class="{{ $field }}" value="{{ old('title', $quiz->title) }}" required>
        <x-input-error :messages="$errors->get('title')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="description" value="Description (optional)" />
        <textarea id="description" name="description" rows="2" class="{{ $field }}">{{ old('description', $quiz->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="instructions" value="Instructions for students" />
        <textarea id="instructions" name="instructions" rows="3" class="{{ $field }}">{{ old('instructions', $quiz->instructions) }}</textarea>
        <x-input-error :messages="$errors->get('instructions')" class="mt-1" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <x-input-label for="start_at" value="Opens at" />
            <input id="start_at" name="start_at" type="datetime-local" class="{{ $field }}"
                   value="{{ old('start_at', $quiz->start_at?->format('Y-m-d\TH:i')) }}">
            <x-input-error :messages="$errors->get('start_at')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="end_at" value="Closes at (deadline)" />
            <input id="end_at" name="end_at" type="datetime-local" class="{{ $field }}"
                   value="{{ old('end_at', $quiz->end_at?->format('Y-m-d\TH:i')) }}">
            <x-input-error :messages="$errors->get('end_at')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="time_limit_minutes" value="Time limit (minutes, optional)" />
            <input id="time_limit_minutes" name="time_limit_minutes" type="number" min="1" class="{{ $field }}"
                   value="{{ old('time_limit_minutes', $quiz->time_limit_minutes) }}">
            <x-input-error :messages="$errors->get('time_limit_minutes')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="passing_score" value="Passing score (%)" />
            <input id="passing_score" name="passing_score" type="number" step="0.01" min="0" max="100" class="{{ $field }}"
                   value="{{ old('passing_score', $quiz->passing_score) }}" required>
            <x-input-error :messages="$errors->get('passing_score')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="max_attempts" value="Maximum attempts" />
            <input id="max_attempts" name="max_attempts" type="number" min="1" max="10" class="{{ $field }}"
                   value="{{ old('max_attempts', $quiz->max_attempts) }}" required>
            <x-input-error :messages="$errors->get('max_attempts')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="result_visibility" value="Show results to students" />
            <select id="result_visibility" name="result_visibility" class="{{ $field }}">
                @foreach (['immediate' => 'Immediately after submitting', 'after_close' => 'After the quiz closes', 'hidden' => 'Hidden'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('result_visibility', $quiz->result_visibility) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('result_visibility')" class="mt-1" />
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="allow_review" value="1" class="rounded border-gray-300"
               @checked(old('allow_review', $quiz->allow_review))>
        Allow students to review previous attempts
    </label>
</div>
