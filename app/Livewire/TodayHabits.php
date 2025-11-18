<?php

namespace App\Livewire;

use App\Models\Habit;
use Livewire\Component;

class TodayHabits extends Component
{
    public function completeHabit(int $habitId): void
    {
        $habit = Habit::findOrFail($habitId);
        
        if ($habit->user_id !== auth()->id()) {
            abort(403);
        }

        $habit->completeForToday();
        
        $this->dispatch('habit-completed');
    }

    public function render()
    {
        $habits = Habit::query()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('livewire.today-habits', [
            'habits' => $habits,
        ]);
    }
}
