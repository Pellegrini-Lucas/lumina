<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/app')->assertRedirect('/login');
});

test('authenticated users can visit the app', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/app')->assertStatus(200);
});
