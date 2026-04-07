<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

expect()->extend('toBeValidEmail', function () {
    return $this->toMatch('/^[\w\.-]+@[\w\.-]+\.\w+$/');
});

expect()->extend('toBeValidUrl', function () {
    return $this->toMatch('/^https?:\/\/.+/');
});

expect()->extend('toHaveKey', function ($key) {
    return $this->toHaveKeys([$key]);
});

expect()->extend('toHaveKeys', function ($keys) {
    foreach ($keys as $key) {
        $this->toHaveKey($key);
    }
    return $this;
});

expect()->extend('toBeSuccessful', function () {
    return $this->toBeInRange(200, 299);
});

expect()->extend('toBeRedirect', function () {
    return $this->toBeInRange(300, 399);
});

expect()->extend('toBeClientError', function () {
    return $this->toBeInRange(400, 499);
});

expect()->extend('toBeServerError', function () {
    return $this->toBeInRange(500, 599);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the amount of code duplication.
|
*/

function createUser(array $attributes = []): \App\Models\User
{
    return \App\Models\User::factory()->create($attributes);
}

function createDocument(array $attributes = []): \App\Models\Dokumen
{
    return \App\Models\Dokumen::factory()->create($attributes);
}

function createKriteria(array $attributes = []): \App\Models\Kriteria
{
    return \App\Models\Kriteria::factory()->create($attributes);
}

function createTemplate(array $attributes = []): \App\Models\DocumentTemplate
{
    return \App\Models\DocumentTemplate::factory()->create($attributes);
}

function createComment(array $attributes = []): \App\Models\Comment
{
    return \App\Models\Comment::factory()->create($attributes);
}

function actingAs(\App\Models\User $user = null): \App\Models\User
{
    $user ??= createUser();
    test()->actingAs($user);
    return $user;
}

function assertDatabaseHas($table, array $data, $connection = null)
{
    test()->assertDatabaseHas($table, $data, $connection);
}

function assertDatabaseMissing($table, array $data, $connection = null)
{
    test()->assertDatabaseMissing($table, $data, $connection);
}

function assertAuthenticated(?string $guard = null)
{
    test()->assertAuthenticated($guard);
}

function assertGuest(?string $guard = null)
{
    test()->assertGuest($guard);
}
