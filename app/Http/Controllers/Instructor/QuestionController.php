<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    public function store(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        if ($quiz->attempts()->exists()) {
            return back()->withErrors(['question_text' => 'Students already attempted this quiz, so questions can no longer be changed.']);
        }

        $data = $request->validate([
            'type' => ['required', 'in:multiple_choice,true_false'],
            'question_text' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'feedback' => ['nullable', 'string'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'string', 'max:255'],
            'correct' => ['nullable', 'integer'],
            'tf_answer' => ['nullable', 'in:true,false'],
        ]);

        $choices = [];

        if ($data['type'] === 'multiple_choice') {
            $options = collect($request->input('options', []))
                ->map(fn ($t) => trim((string) $t))
                ->filter(fn ($t) => $t !== '');

            if ($options->count() < 2) {
                throw ValidationException::withMessages(['options' => 'Provide at least two answer choices.']);
            }
            if (! $request->filled('correct') || ! $options->has((int) $request->input('correct'))) {
                throw ValidationException::withMessages(['correct' => 'Select the correct answer beside one of the filled choices.']);
            }

            foreach ($options as $index => $text) {
                $choices[] = ['text' => $text, 'correct' => $index === (int) $request->input('correct')];
            }
        } else {
            if (! $request->filled('tf_answer')) {
                throw ValidationException::withMessages(['tf_answer' => 'Select whether the correct answer is True or False.']);
            }
            $choices = [
                ['text' => 'True', 'correct' => $data['tf_answer'] === 'true'],
                ['text' => 'False', 'correct' => $data['tf_answer'] === 'false'],
            ];
        }

        DB::transaction(function () use ($quiz, $data, $choices) {
            $question = $quiz->questions()->create([
                'type' => $data['type'],
                'question_text' => $data['question_text'],
                'feedback' => $data['feedback'] ?? null,
                'points' => $data['points'],
                'position' => (Question::where('quiz_id', $quiz->id)->max('position') ?? 0) + 1,
            ]);

            foreach ($choices as $i => $choice) {
                $question->options()->create([
                    'option_text' => $choice['text'],
                    'is_correct' => $choice['correct'],
                    'position' => $i + 1,
                ]);
            }
        });

        return back()->with('status', 'Question added.');
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        $this->authorizeQuiz($quiz);
        abort_unless($question->quiz_id === $quiz->id, 404);

        if ($quiz->attempts()->exists()) {
            return back()->withErrors(['question_text' => 'Students already attempted this quiz, so questions can no longer be changed.']);
        }

        $question->delete();

        return back()->with('status', 'Question deleted.');
    }

    private function authorizeQuiz(Quiz $quiz): void
    {
        abort_unless($quiz->instructor_id === auth()->id(), 403);
    }
}
