<?php

use App\Models\User;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request as HttpRequest;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as GoogleUser;

beforeEach(function () {
    config(['services.google' => [
        'client_id' => 'test-client', 'client_secret' => 'test-secret',
        'redirect' => 'http://localhost/auth/google/callback',
    ]]);
});

function mockGoogleIdentity(array $overrides = []): GoogleUser
{
    return (new GoogleUser)->setRaw(array_merge(['email_verified' => true], $overrides['raw'] ?? []))
        ->map(array_merge(['id' => 'google-123', 'name' => 'Google User', 'email' => 'google@example.com'], $overrides));
}

function mockGoogleCallback(GoogleUser $identity): void
{
    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('user')->once()->andReturn($identity);
    Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);
}

test('login and register show Google sign in without removing password forms', function () {
    $this->get(route('login'))->assertOk()->assertSee('Continue with Google')->assertSee('name="password"', false);
    $this->get(route('register'))->assertOk()->assertSee('Continue with Google')->assertSee('name="password_confirmation"', false);
});

test('Google redirect delegates to the stateful Socialite provider', function () {
    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com/oauth'));
    Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);
    $this->get(route('google.redirect'))->assertRedirect('https://accounts.google.com/oauth');
});

test('real provider redirect stores state in session', function () {
    $response = $this->get(route('google.redirect'));
    $response->assertRedirect()->assertSessionHas('state');
    expect($response->headers->get('Location'))->toStartWith('https://accounts.google.com/');
});

test('unconfigured Google returns a useful error', function (string $key) {
    config(['services.google.'.$key => null]);
    Socialite::shouldReceive('driver')->never();
    $this->get(route('google.redirect'))->assertRedirect(route('login'))->assertSessionHasErrors('google');
    $this->get(route('google.callback', ['code' => 'test']))->assertRedirect(route('login'))->assertSessionHasErrors('google');
    $this->assertGuest();
})->with(['client_id', 'client_secret', 'redirect']);

test('new Google user is registered verified and logged in without storing tokens', function () {
    mockGoogleCallback(mockGoogleIdentity(['email' => 'GOOGLE@example.com', 'token' => 'secret-token']));
    $this->withSession(['marker' => 'preserve'])->get(route('google.callback', ['code' => 'test']))
        ->assertRedirect(route('dashboard', absolute: false))->assertSessionHas('marker', 'preserve');
    $user = User::where('google_id', 'google-123')->firstOrFail();
    $this->assertAuthenticatedAs($user);
    expect($user->email)->toBe('google@example.com')->and($user->password)->toBeNull()
        ->and($user->email_verified_at)->not->toBeNull();
    expect($user->getAttributes())->not->toHaveKey('google_token');
});

test('existing Google user is found by provider id and local profile is preserved', function () {
    $user = User::factory()->create(['email' => 'original@example.com', 'name' => 'Local Name']);
    $user->forceFill(['google_id' => 'google-123'])->save();
    mockGoogleCallback(mockGoogleIdentity(['email' => 'changed@example.com']));
    $this->get(route('google.callback', ['code' => 'test']))->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->email)->toBe('original@example.com')->and($user->fresh()->name)->toBe('Local Name');
    $this->assertDatabaseCount('users', 1);
});

test('same email never takes over or automatically links a password or different Google account', function (?string $googleId) {
    $user = User::factory()->create(['email' => 'google@example.com']);
    $user->forceFill(['google_id' => $googleId])->save();
    $password = $user->password;
    mockGoogleCallback(mockGoogleIdentity());
    $this->get(route('google.callback', ['code' => 'test']))->assertRedirect(route('login'))->assertSessionHasErrors('google');
    $this->assertGuest();
    expect($user->fresh()->google_id)->toBe($googleId)->and($user->fresh()->password)->toBe($password);
    $this->assertDatabaseCount('users', 1);
})->with([null, 'different-google-id']);

test('Google callback rejects invalid identity data', function (array $overrides) {
    mockGoogleCallback(mockGoogleIdentity($overrides));
    $this->get(route('google.callback', ['code' => 'test']))->assertRedirect(route('login'))->assertSessionHasErrors('google');
    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
})->with([
    'unverified' => [['raw' => ['email_verified' => false]]],
    'string verified flag' => [['raw' => ['email_verified' => 'true']]],
    'missing id' => [['id' => null]],
    'invalid email' => [['email' => 'invalid']],
    'oversized id' => [['id' => str_repeat('a', 256)]],
]);

test('cancelled and incomplete callback clears pending state', function (array $query) {
    Socialite::shouldReceive('driver')->never();
    $this->withSession(['state' => 'pending'])->get(route('google.callback', $query))
        ->assertRedirect(route('login'))->assertSessionHasErrors('google')->assertSessionMissing('state');
    $this->assertGuest();
})->with([
    'cancelled' => [['error' => 'access_denied']],
    'missing code' => [[]],
]);

test('callback without matching state fails before any Google network request', function () {
    $this->withSession(['state' => 'expected'])->get(route('google.callback', ['code' => 'test', 'state' => 'forged']))
        ->assertRedirect(route('login'))->assertSessionHasErrors('google');
    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
});

test('provider errors and expired sessions are handled without leaking details', function (string $failure) {
    $provider = Mockery::mock(GoogleProvider::class);
    $exception = match ($failure) {
        'state' => new InvalidStateException,
        'network' => new ConnectException('secret-token', new HttpRequest('GET', 'https://google.test')),
        default => new Exception('secret-token'),
    };
    $provider->shouldReceive('user')->once()->andThrow($exception);
    Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);
    $this->get(route('google.callback', ['code' => 'test']))->assertRedirect(route('login'))->assertSessionHasErrors('google');
    $this->get(route('login'))->assertDontSee('secret-token');
    $this->assertGuest();
})->with(['state', 'network', 'invalid JWT']);

test('provider redirect errors are handled', function () {
    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('redirect')->andThrow(new Exception('secret'));
    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    $this->get(route('google.redirect'))->assertRedirect(route('login'))->assertSessionHasErrors('google');
});

test('Google login regenerates the session and honors intended destination', function () {
    $this->get(route('transactions.index'))->assertRedirect(route('login'));
    $previousId = session()->getId();
    mockGoogleCallback(mockGoogleIdentity());
    $this->get(route('google.callback', ['code' => 'test']))->assertRedirect(route('transactions.index'));
    expect(session()->getId())->not->toBe($previousId);
    $this->get(route('dashboard'))->assertOk();
    $this->get(route('profile.edit'))->assertOk()->assertSee('Set a password via email');
    $this->post(route('logout'))->assertRedirect('/');
    $this->assertGuest();
});

test('Google-only users cannot log in with an arbitrary password', function () {
    $user = User::factory()->create(['password' => null]);
    $user->forceFill(['google_id' => 'google-123'])->save();
    $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('Google-only users can set a password through an email reset and use password authentication', function () {
    Notification::fake();
    $user = User::factory()->create(['password' => null]);
    $user->forceFill(['google_id' => 'google-123'])->save();
    $this->post(route('password.email'), ['email' => $user->email])->assertSessionHasNoErrors();
    Notification::assertSentTo($user, ResetPassword::class);
    $token = Password::createToken($user);
    $this->post(route('password.store'), ['token' => $token, 'email' => $user->email, 'password' => 'secure-password', 'password_confirmation' => 'secure-password'])
        ->assertRedirect(route('login'))->assertSessionHasNoErrors();
    expect(Hash::check('secure-password', $user->fresh()->password))->toBeTrue();
    $this->post(route('login'), ['email' => $user->email, 'password' => 'secure-password'])->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($user);
    $this->delete(route('profile.destroy'), ['password' => 'secure-password'])->assertRedirect('/');
    $this->assertGuest();
    $this->assertModelMissing($user);
});

test('Google profiles cannot change email or provider identity via profile input', function () {
    $user = User::factory()->create();
    $user->forceFill(['google_id' => 'google-123'])->save();
    $this->actingAs($user)->patch(route('profile.update'), ['name' => 'Updated', 'email' => 'attacker@example.com'])
        ->assertSessionHasErrors('email');
    $this->patch(route('profile.update'), ['name' => 'Updated', 'email' => $user->email, 'google_id' => 'attacker'])
        ->assertSessionHasNoErrors();
    expect($user->fresh()->google_id)->toBe('google-123')->and($user->fresh()->name)->toBe('Updated');
});

test('authenticated users cannot switch accounts through guest Google routes', function () {
    $user = User::factory()->create();
    Socialite::shouldReceive('driver')->never();
    $this->actingAs($user)->get(route('google.redirect'))->assertRedirect(route('dashboard', absolute: false));
    $this->get(route('google.callback', ['code' => 'test']))->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticatedAs($user);
});

test('Google identity is unique in the database', function () {
    User::factory()->create()->forceFill(['google_id' => 'google-123'])->save();
    expect(fn () => User::factory()->create()->forceFill(['google_id' => 'google-123'])->save())
        ->toThrow(UniqueConstraintViolationException::class);
});

test('authenticated Google password setup sends only to the account owner and signs out', function () {
    Notification::fake();
    $user = User::factory()->create(['password' => null]);
    $user->forceFill(['google_id' => 'google-123'])->save();
    $this->actingAs($user)->post(route('password.setup'), ['email' => 'attacker@example.com'])
        ->assertRedirect(route('login'))->assertSessionHas('status');
    Notification::assertSentTo($user, ResetPassword::class);
    $this->assertGuest();
});

test('password setup requires a Google-only authenticated account', function () {
    $this->post(route('password.setup'))->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create())->post(route('password.setup'))->assertForbidden();
});
