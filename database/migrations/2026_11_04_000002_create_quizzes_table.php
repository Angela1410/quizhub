<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->unsignedSmallInteger('time_limit_minutes')->nullable();
            $table->decimal('passing_score', 5, 2)->default(60.00); // percent
            $table->unsignedTinyInteger('max_attempts')->default(1);
            $table->enum('result_visibility', ['immediate', 'after_close', 'hidden'])->default('immediate');
            $table->boolean('allow_review')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
