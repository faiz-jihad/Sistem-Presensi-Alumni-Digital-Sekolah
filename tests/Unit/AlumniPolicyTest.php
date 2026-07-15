<?php

use App\Models\Alumni;
use App\Models\User;
use App\Policies\AlumniPolicy;

test('alumni dapat melihat profil miliknya meskipun status sekolah tidak tersedia', function () {
    $user = new User([
        'name' => 'Alumni',
        'email' => 'alumni@example.com',
        'role' => 'alumni',
    ]);
    $user->id = 10;

    $alumni = new Alumni;
    $alumni->user_id = 10;

    $policy = new AlumniPolicy;

    expect($policy->before($user, 'viewProfile'))->toBeNull()
        ->and($policy->viewProfile($user, $alumni))->toBeTrue();
});
