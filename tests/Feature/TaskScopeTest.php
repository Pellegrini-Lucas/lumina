<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;

test('authenticated user only sees their own tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Crear tareas para cada usuario
    Task::factory()->count(3)->create(['user_id' => $user1->id]);
    Task::factory()->count(2)->create(['user_id' => $user2->id]);

    // Autenticar como user1
    $this->actingAs($user1);

    // Verificar que solo se obtienen las tareas de user1
    $tasks = Task::all();
    expect($tasks)->toHaveCount(3)
        ->and($tasks->pluck('user_id')->unique()->toArray())->toBe([$user1->id]);
});

test('authenticated user only sees their own projects', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Crear proyectos para cada usuario
    \App\Models\Project::factory()->count(2)->create(['user_id' => $user1->id]);
    \App\Models\Project::factory()->count(3)->create(['user_id' => $user2->id]);

    // Autenticar como user1
    $this->actingAs($user1);

    // Verificar que solo se obtienen los proyectos de user1
    $projects = \App\Models\Project::all();
    expect($projects)->toHaveCount(2)
        ->and($projects->pluck('user_id')->unique()->toArray())->toBe([$user1->id]);
});

test('authenticated user only sees their own events', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Crear eventos para cada usuario
    \App\Models\Event::factory()->count(4)->create(['user_id' => $user1->id]);
    \App\Models\Event::factory()->count(1)->create(['user_id' => $user2->id]);

    // Autenticar como user1
    $this->actingAs($user1);

    // Verificar que solo se obtienen los eventos de user1
    $events = \App\Models\Event::all();
    expect($events)->toHaveCount(4)
        ->and($events->pluck('user_id')->unique()->toArray())->toBe([$user1->id]);
});

test('user cannot see tasks from other users in query', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Crear tareas para user2
    Task::factory()->count(5)->create(['user_id' => $user2->id]);

    // Autenticar como user1
    $this->actingAs($user1);

    // Verificar que user1 no ve las tareas de user2
    $tasks = Task::all();
    expect($tasks)->toHaveCount(0);
});
