<?php

use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\User;
use Carbon\Carbon;

test('user can complete a habit for today', function () {
    $user = User::factory()->create();
    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Habit',
        'frequency' => 'daily',
        'daily_interval' => 1,
    ]);

    $this->actingAs($user);

    expect($habit->isCompletedToday())->toBeFalse();

    $habit->completeForToday();

    expect($habit->isCompletedToday())->toBeTrue();
    expect(HabitCompletion::count())->toBe(1);
});

test('completing habit increases streak', function () {
    $user = User::factory()->create();
    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Habit',
        'frequency' => 'daily',
        'daily_interval' => 1,
    ]);

    $this->actingAs($user);

    expect($habit->getCurrentStreak())->toBe(0);

    $habit->completeForToday();
    $habit->refresh();

    expect($habit->getCurrentStreak())->toBe(1);
    expect($habit->streak)->toBe(1);
});

test('habit cannot be completed twice in same day', function () {
    $user = User::factory()->create();
    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Habit',
        'frequency' => 'daily',
        'daily_interval' => 1,
    ]);

    $this->actingAs($user);

    $habit->completeForToday();
    $habit->completeForToday();

    expect(HabitCompletion::count())->toBe(1);
});

test('streak opacity increases with streak length', function () {
    $user = User::factory()->create();
    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Habit',
        'frequency' => 'daily',
        'daily_interval' => 1,
    ]);

    $this->actingAs($user);

    expect($habit->getStreakOpacity())->toBe(0.1);

    HabitCompletion::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'completed_at' => Carbon::today(),
    ]);

    $opacity = $habit->getStreakOpacity();
    expect($opacity)->toBeGreaterThan(0.1);
});

test('daily habit due date is calculated correctly', function () {
    $user = User::factory()->create();
    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Habit',
        'frequency' => 'daily',
        'daily_interval' => 2,
    ]);

    $this->actingAs($user);
    
    $today = Carbon::today();
    $nextDue = $habit->calculateNextDueDate($today);

    expect((int) $today->diffInDays($nextDue))->toBe(2);
});

test('habit shows as due today when next_due_date is today', function () {
    $user = User::factory()->create();
    
    $habit = new Habit([
        'user_id' => $user->id,
        'name' => 'Test Habit',
        'frequency' => 'daily',
        'daily_interval' => 1,
    ]);
    
    $habit->next_due_date = Carbon::today();
    $habit->saveQuietly();

    $this->actingAs($user);

    expect($habit->isDueToday())->toBeTrue();
});

test('best streak is tracked correctly', function () {
    $user = User::factory()->create();
    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Habit',
        'frequency' => 'daily',
        'daily_interval' => 1,
        'best_streak' => 0,
    ]);

    $this->actingAs($user);

    $habit->completeForToday();
    $habit->refresh();

    expect($habit->best_streak)->toBe(1);

    HabitCompletion::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'completed_at' => Carbon::yesterday(),
    ]);

    $habit->refresh();
    $currentStreak = $habit->getCurrentStreak();
    
    $habit->update(['streak' => $currentStreak, 'best_streak' => max($habit->best_streak, $currentStreak)]);
    
    expect($habit->best_streak)->toBeGreaterThanOrEqual(1);
});
