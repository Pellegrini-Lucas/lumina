<?php

namespace App\Models;

use App\Enums\HabitFrequency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'frequency',
        'daily_interval',
        'weekly_days',
        'weekly_interval',
        'monthly_days',
        'monthly_interval',
        'last_completed_at',
        'next_due_date',
        'streak',
        'best_streak',
        'total_completions',
    ];

    protected function casts(): array
    {
        return [
            'frequency' => HabitFrequency::class,
            'weekly_days' => 'array',
            'monthly_days' => 'array',
            'last_completed_at' => 'date',
            'next_due_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(HabitCompletion::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('userHabits', function ($query) {
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            }
        });

        static::creating(function ($habit) {
            $habit->setNextDueDateOnCreation();
        });

        static::updating(function ($habit) {
            // Si cambió la frecuencia o los días, recalcular next_due_date
            if ($habit->isDirty(['frequency', 'daily_interval', 'weekly_days', 'weekly_interval', 'monthly_days', 'monthly_interval'])) {
                $habit->setNextDueDateOnCreation();
            }
        });
    }

    public function getCurrentStreak(): int
    {
        $completions = $this->completions()
            ->orderBy('completed_at', 'desc')
            ->limit(100)
            ->get();

        if ($completions->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $lastDate = null;

        foreach ($completions as $completion) {
            $completedAt = Carbon::parse($completion->completed_at);
            
            if ($streak === 0) {
                // Primera completación - debe ser hoy o ayer
                if ($completedAt->isToday() || $completedAt->isYesterday()) {
                    $streak = 1;
                    $lastDate = $completedAt;
                } else {
                    break;
                }
            } else {
                // Verificar que sea consecutivo según la frecuencia
                $daysDiff = $lastDate->diffInDays($completedAt);
                
                if ($this->frequency === HabitFrequency::Daily) {
                    if ($daysDiff === ($this->daily_interval ?? 1)) {
                        $streak++;
                        $lastDate = $completedAt;
                    } else {
                        break;
                    }
                } else {
                    // Para semanal y mensual, simplificar la verificación
                    if ($daysDiff >= 1 && $daysDiff <= 31) {
                        $streak++;
                        $lastDate = $completedAt;
                    } else {
                        break;
                    }
                }
            }
        }

        return $streak;
    }

    public function isDueToday(): bool
    {
        if (!$this->next_due_date) {
            return true;
        }

        return Carbon::parse($this->next_due_date)->isToday() || 
               Carbon::parse($this->next_due_date)->isPast();
    }

    public function isCompletedToday(): bool
    {
        return $this->completions()
            ->whereDate('completed_at', Carbon::today())
            ->exists();
    }

    public function setNextDueDateOnCreation(): void
    {
        $today = Carbon::today();
        
        // Si ya completó hoy, calcular desde mañana
        $startDate = $this->isCompletedToday() ? $today->copy()->addDay() : $today;
        
        if ($this->frequency === HabitFrequency::Weekly && !empty($this->weekly_days)) {
            if (!$this->isCompletedToday() && in_array($today->dayOfWeek, $this->weekly_days)) {
                $this->next_due_date = $today;
            } else {
                $this->next_due_date = $this->calculateNextDueDate($startDate);
            }
        } elseif ($this->frequency === HabitFrequency::Monthly && !empty($this->monthly_days)) {
            if (!$this->isCompletedToday() && in_array($today->day, $this->monthly_days)) {
                $this->next_due_date = $today;
            } else {
                $this->next_due_date = $this->calculateNextDueDate($startDate);
            }
        } else {
            // Diario o sin días específicos: empezar desde hoy si no está completado
            if (!$this->isCompletedToday()) {
                $this->next_due_date = $today;
            } else {
                $this->next_due_date = $this->calculateNextDueDate($startDate);
            }
        }
    }

    public function completeForToday(): void
    {
        if ($this->isCompletedToday()) {
            return;
        }

        HabitCompletion::create([
            'user_id' => $this->user_id,
            'habit_id' => $this->id,
            'completed_at' => Carbon::today(),
        ]);

        // Refrescar la relación para incluir la nueva completación
        $this->load('completions');
        $newStreak = $this->getCurrentStreak();
        
        $this->update([
            'last_completed_at' => Carbon::today(),
            'next_due_date' => $this->calculateNextDueDate(Carbon::today()),
            'streak' => $newStreak,
            'best_streak' => max($this->best_streak ?? 0, $newStreak),
            'total_completions' => ($this->total_completions ?? 0) + 1,
        ]);
    }

    public function calculateNextDueDate(Carbon $fromDate): Carbon
    {
        return match ($this->frequency) {
            HabitFrequency::Daily => $fromDate->copy()->addDays($this->daily_interval ?? 1),
            HabitFrequency::Weekly => $this->calculateNextWeeklyDate($fromDate),
            HabitFrequency::Monthly => $this->calculateNextMonthlyDate($fromDate),
        };
    }

    protected function calculatePreviousDueDate(Carbon $fromDate): Carbon
    {
        return match ($this->frequency) {
            HabitFrequency::Daily => $fromDate->copy()->subDays($this->daily_interval ?? 1),
            HabitFrequency::Weekly => $fromDate->copy()->subWeeks($this->weekly_interval ?? 1),
            HabitFrequency::Monthly => $fromDate->copy()->subMonths($this->monthly_interval ?? 1),
        };
    }

    protected function calculateNextWeeklyDate(Carbon $fromDate): Carbon
    {
        if (empty($this->weekly_days)) {
            return $fromDate->copy()->addWeeks($this->weekly_interval ?? 1);
        }

        $interval = $this->weekly_interval ?? 1;
        
        // Buscar el próximo día válido en los próximos 7 días desde fromDate
        $nextDate = $fromDate->copy();
        
        for ($i = 0; $i < 7; $i++) {
            if (in_array($nextDate->dayOfWeek, $this->weekly_days)) {
                return $nextDate;
            }
            $nextDate->addDay();
        }

        // Si no encontró ninguno (no debería pasar), usar fallback
        return $fromDate->copy()->addWeeks($interval);
    }

    protected function calculateNextMonthlyDate(Carbon $fromDate): Carbon
    {
        if (empty($this->monthly_days)) {
            return $fromDate->copy()->addMonths($this->monthly_interval ?? 1);
        }

        // Buscar el próximo día válido en el mes siguiente
        $nextMonth = $fromDate->copy()->addMonth()->startOfMonth();
        
        foreach ($this->monthly_days as $day) {
            $candidateDate = $nextMonth->copy()->day(min($day, $nextMonth->daysInMonth));
            
            if ($candidateDate->greaterThan($fromDate)) {
                return $candidateDate;
            }
        }

        // Fallback: agregar el intervalo de meses configurado
        return $fromDate->copy()->addMonths($this->monthly_interval ?? 1);
    }

    public function getStreakColor(): string
    {
        $streak = $this->getCurrentStreak();
        
        // Progresión suave de color de negro a violeta en 30 días
        // Negro: rgb(0, 0, 0) -> Violeta: rgb(139, 92, 246)
        $progress = min($streak / 30.0, 1.0);
        
        $r = (int)(139 * $progress);
        $g = (int)(92 * $progress);
        $b = (int)(246 * $progress);
        
        // Opacidad aumenta gradualmente de 0.1 a 1.0
        $opacity = 0.1 + ($progress * 0.9);
        
        return "rgba({$r}, {$g}, {$b}, {$opacity})";
    }
}
