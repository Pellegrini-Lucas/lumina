<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;

test('user can view their own tasks', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    expect($user->can('view', $task))->toBeTrue();
});

test('user cannot view other users tasks', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    expect($user->can('view', $task))->toBeFalse();
});

test('user can update their own tasks', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    expect($user->can('update', $task))->toBeTrue();
});

test('user cannot update other users tasks', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    expect($user->can('update', $task))->toBeFalse();
});

test('user can delete their own tasks', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    expect($user->can('delete', $task))->toBeTrue();
});

test('user cannot delete other users tasks', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    expect($user->can('delete', $task))->toBeFalse();
});
