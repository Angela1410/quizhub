<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = auth()->user()->quizzes()->withCount('questions')->latest()->paginate(10);

        return view('instructor.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $quiz = new Quiz(['passing_score' => 60, 'max_attempts' => 1, 'result_visibility' => 'immediate']);

        return view('instructor.quizzes.create', compact('quiz'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['allow_review'] = $request->boolean('allow_review');
        $data['status'] = 'draft';

        $quiz = auth()->user()->quizzes()->create($data);

        return redirect()->route('instructor.quizzes.show', $quiz)
            ->with('status', 'Quiz created. Now add your questions.');
    }

    // The "show" page is where the instructor manages the quiz's questions.
    public function show(Quiz $quiz)
    {
        $this->owned($quiz);
        $quiz->load('questions.options');

        return view('instructor.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        $this->owned($quiz);

        return view('instructor.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $this->owned($quiz);
        $data = $request->validate($this->rules());
        $data['allow_review'] = $request->boolean('allow_review');

        $quiz->update($data);

        return redirect()->route('instructor.quizzes.show', $quiz)->with('status', 'Quiz updated.');
    }

    public function destroy(Quiz $quiz)
    {
        $this->owned($quiz);

        if ($quiz->attempts()->exists()) {
            return back()->withErrors(['quiz' => 'This quiz already has student attempts and cannot be deleted. Archive it instead.']);
        }

        $quiz->delete();

        return redirect()->route('instructor.quizzes.index')->with('status', 'Quiz deleted.');
    }

    // Publish / unpublish / archive / restore
    public function updateStatus(Request $request, Quiz $quiz)
    {
        $this->owned($quiz);
        $data = $request->validate(['status' => ['required', 'in:draft,published,archived']]);

        if ($data['status'] === 'published' && $quiz->questions()->count() === 0) {
            return back()->withErrors(['status' => 'Add at least one question before publishing.']);
        }

        $quiz->update(['status' => $data['status']]);

        return back()->with('status', 'Quiz status changed to ' . $data['status'] . '.');
    }

    // Instructors can only touch their own quizzes.
    private function owned(Quiz $quiz): void
    {
        abort_unless($quiz->instructor_id === auth()->id(), 403);
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after:start_at'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'passing_score' => ['required', 'numeric', 'between:0,100'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'result_visibility' => ['required', 'in:immediate,after_close,hidden'],
        ];
    }
}
