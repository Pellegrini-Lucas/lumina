<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('user', function ($query) {
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            }
        });

        // Actualizar completed_at automáticamente cuando se marca como completado
        static::updating(function (Task $task) {
            if ($task->isDirty('status')) {
                if ($task->status === TaskStatus::Completado && ! $task->completed_at) {
                    $task->completed_at = now();
                } elseif ($task->status !== TaskStatus::Completado) {
                    $task->completed_at = null;
                }
            }
        });
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        if (! $this->due_date) {
            return false;
        }

        // No está vencida si ya fue completada o cancelada
        if (in_array($this->status, [TaskStatus::Completado, TaskStatus::Cancelada])) {
            return false;
        }

        return $this->due_date->isPast();
    }

    /**
     * Get the user that owns the task
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project that owns the task
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subtasks for the task
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class);
    }
}
