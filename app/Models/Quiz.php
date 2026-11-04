<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'instructor_id', 'title', 'description', 'instructions', 'status',
        'start_at', 'end_at', 'time_limit_minutes', 'passing_score',
        'max_attempts', 'result_visibility', 'allow_review',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'allow_review' => 'boolean',
            'passing_score' => 'decimal:2',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    /** Published quizzes that are inside their schedule window. */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('start_at')->orWhere('start_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('end_at')->orWhere('end_at', '>=', now()));
    }

    public function totalPoints(): int
    {
        return (int) $this->questions()->sum('points');
    }
}
