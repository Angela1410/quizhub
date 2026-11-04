<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Sample accounts (all use the password: password)
        User::create(['name' => 'System Admin', 'email' => 'admin@quizhub.test', 'password' => 'password', 'role' => 'admin']);
        $instructor = User::create(['name' => 'Sample Instructor', 'email' => 'instructor@quizhub.test', 'password' => 'password', 'role' => 'instructor']);
        User::create(['name' => 'Student One', 'email' => 'student1@quizhub.test', 'password' => 'password', 'role' => 'student']);
        User::create(['name' => 'Student Two', 'email' => 'student2@quizhub.test', 'password' => 'password', 'role' => 'student']);

        // Sample quiz
        $quiz = Quiz::create([
            'instructor_id' => $instructor->id,
            'title' => 'Web Development Basics',
            'description' => 'A short quiz on web fundamentals.',
            'instructions' => 'Answer all questions. You have 20 minutes. Each question has one correct answer.',
            'status' => 'published',
            'start_at' => now()->subDay(),
            'end_at' => now()->addDays(30),
            'time_limit_minutes' => 20,
            'passing_score' => 60,
            'max_attempts' => 2,
            'result_visibility' => 'immediate',
            'allow_review' => true,
        ]);

        // [type, question, points, feedback, [options], index of correct option]
        $questions = [
            ['multiple_choice', 'What does HTML stand for?', 1, 'HTML = HyperText Markup Language.',
                ['HyperText Markup Language', 'High Tech Modern Language', 'Home Tool Markup Language', 'Hyperlinks and Text Mark Language'], 0],
            ['multiple_choice', 'Which language is used to style web pages?', 1, 'CSS controls styling.',
                ['PHP', 'CSS', 'SQL', 'Python'], 1],
            ['multiple_choice', 'Which PHP framework uses the MVC pattern and Artisan CLI?', 1, 'Laravel provides Artisan.',
                ['Django', 'Laravel', 'Express', 'Flask'], 1],
            ['true_false', 'HTTP is a stateless protocol.', 1, 'Each HTTP request is independent.',
                ['True', 'False'], 0],
            ['true_false', 'A primary key can contain duplicate values.', 1, 'Primary keys must be unique.',
                ['True', 'False'], 1],
        ];

        foreach ($questions as $i => [$type, $text, $points, $feedback, $options, $correct]) {
            $q = Question::create([
                'quiz_id' => $quiz->id, 'type' => $type, 'question_text' => $text,
                'points' => $points, 'feedback' => $feedback, 'position' => $i + 1,
            ]);
            foreach ($options as $j => $optionText) {
                Option::create([
                    'question_id' => $q->id, 'option_text' => $optionText,
                    'is_correct' => $j === $correct, 'position' => $j + 1,
                ]);
            }
        }
    }
}
