<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Notifications\Auth\VerifyEmailNotification;
use App\Notifications\TeacherVerificationAdminNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EasyPsiPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_french_homepage(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/fr');
    }

    public function test_arabic_login_page_loads(): void
    {
        $response = $this->get('/ar/login');

        $response->assertOk();
        $response->assertSee('الدخول إلى حسابك');
        $response->assertSee('تسجيل الدخول');
    }

    public function test_french_login_page_loads(): void
    {
        $response = $this->get('/fr/login');

        $response->assertOk();
        $response->assertSee('Connexion à votre compte');
        $response->assertDontSee('Espace admin');
    }

    public function test_english_login_page_loads(): void
    {
        $response = $this->get('/en/login');

        $response->assertOk();
        $response->assertSee('Log in to your account');
        $response->assertDontSee('Admin area');
    }

    public function test_login_required_errors_are_localized_in_french(): void
    {
        $response = $this->from('/fr/login')->post('/fr/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertRedirect('/fr/login');
        $response->assertSessionHasErrors([
            'email' => 'Le champ Adresse email est obligatoire.',
            'password' => 'Le champ Mot de passe est obligatoire.',
        ]);
    }

    public function test_registration_page_loads(): void
    {
        $response = $this->get('/fr/register');

        $response->assertOk();
        $response->assertSee('Créer votre compte');
        $response->assertSee('Téléphone');
    }

    public function test_forgot_password_page_loads(): void
    {
        $response = $this->get('/fr/forgot-password');

        $response->assertOk();
        $response->assertSee('Réinitialiser le mot de passe');
    }

    public function test_existing_user_receives_password_reset_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'preferred_locale' => 'ar',
        ]);

        $response = $this->from('/en/forgot-password')->post('/en/forgot-password', [
            'email' => 'reset@example.com',
        ]);

        $response->assertRedirect('/en/forgot-password');
        Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $notification) use ($user) {
            $mailMessage = $notification->toMail($user);
            $data = $mailMessage->data();

            return $mailMessage->subject === 'EasyPsi | Reset your password'
                && $data['lang'] === 'en'
                && $data['dir'] === 'ltr'
                && str_contains($data['buttonUrl'], '/en/reset-password/');
        });
    }

    public function test_forgot_password_is_rate_limited_after_five_attempts(): void
    {
        Notification::fake();

        User::factory()->create([
            'email' => 'cooldown@example.com',
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $response = $this->from('/en/forgot-password')->post('/en/forgot-password', [
                'email' => 'cooldown@example.com',
            ]);

            $response->assertRedirect('/en/forgot-password');
        }

        $blockedResponse = $this->from('/en/forgot-password')->post('/en/forgot-password', [
            'email' => 'cooldown@example.com',
        ]);

        $blockedResponse->assertRedirect('/en/forgot-password');
        $blockedResponse->assertSessionHasErrors([
            'email' => 'Too many reset requests. Please try again in a few minutes.',
        ]);
    }

    public function test_unknown_email_shows_database_error_on_forgot_password(): void
    {
        Notification::fake();

        $response = $this->from('/fr/forgot-password')->post('/fr/forgot-password', [
            'email' => 'unknown@example.com',
        ]);

        $response->assertRedirect('/fr/forgot-password');
        $response->assertSessionHasErrors([
            'email' => "Cet email n'existe pas dans la base de donnees.",
        ]);
        Notification::assertNothingSent();
    }

    public function test_registration_saves_user_data_in_users_table(): void
    {
        Notification::fake();

        $response = $this->post('/en/register', [
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'phone' => '0600000000',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/en/verify-email');

        $this->assertDatabaseHas('users', [
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'phone' => '0600000000',
            'role' => 'student',
            'preferred_locale' => 'en',
        ]);

        $user = User::where('email', 'student@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_registration_still_succeeds_when_verification_email_fails(): void
    {
        Event::listen(Registered::class, function () {
            throw new \RuntimeException('Mail transport failed.');
        });

        $response = $this->post('/fr/register', [
            'name' => 'Mail Failure User',
            'email' => 'mail-failure@example.com',
            'phone' => '0600000001',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/fr/verify-email');

        $this->assertDatabaseHas('users', [
            'name' => 'Mail Failure User',
            'email' => 'mail-failure@example.com',
            'role' => 'student',
            'preferred_locale' => 'fr',
        ]);

        $this->assertAuthenticatedAs(User::where('email', 'mail-failure@example.com')->firstOrFail());
    }

    public function test_unverified_login_redirects_to_verification_notice(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'unverified-login@example.com',
            'password' => 'secret123',
            'preferred_locale' => 'fr',
        ]);

        $response = $this->post('/fr/login', [
            'email' => 'unverified-login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/fr/verify-email');
        $this->assertAuthenticatedAs($user);
    }

    public function test_unverified_user_is_redirected_from_account_pages_to_verification_notice(): void
    {
        $student = User::factory()->unverified()->create([
            'email' => 'unverified-account@example.com',
            'role' => 'student',
            'preferred_locale' => 'fr',
        ]);

        $this->actingAs($student);

        $response = $this->get('/fr/student-profile');

        $response->assertRedirect('/fr/verify-email');
    }

    public function test_registration_password_requires_eight_characters_and_a_number(): void
    {
        $response = $this->from('/en/register')->post('/en/register', [
            'name' => 'Weak Password User',
            'email' => 'weak@example.com',
            'phone' => '0600000000',
            'password' => 'weakpass',
            'password_confirmation' => 'weakpass',
        ]);

        $response->assertRedirect('/en/register');
        $response->assertSessionHasErrors([
            'password' => 'The Password field must contain at least 8 characters and 1 number.',
        ]);
    }

    public function test_admin_is_notified_after_teacher_verifies_email(): void
    {
        Notification::fake();
        config(['easypsi.admin_email' => 'admin@easypsi.test']);

        $teacher = User::factory()->unverified()->create([
            'name' => 'Future Teacher',
            'email' => 'future-teacher@example.com',
            'phone' => '0612345678',
            'role' => 'teacher',
            'preferred_locale' => 'fr',
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'locale' => 'fr',
                'id' => $teacher->id,
                'hash' => sha1($teacher->getEmailForVerification()),
            ]
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirect('/fr/teacher-pending');
        $this->assertNotNull($teacher->fresh()->email_verified_at);

        Notification::assertSentOnDemand(TeacherVerificationAdminNotification::class, function ($notification, array $channels, object $notifiable) {
            return in_array('mail', $channels, true)
                && ($notifiable->routes['mail'] ?? null) === 'admin@easypsi.test';
        });
    }

    public function test_pending_teacher_cannot_access_teacher_space_until_admin_verifies_them(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'email_verified_at' => now(),
            'teacher_verified_at' => null,
        ]);

        $this->actingAs($teacher);

        $this->get('/fr/teacher-space')->assertRedirect('/fr/teacher-pending');
        $this->get('/fr/teacher-pending')->assertOk();
    }

    public function test_admin_can_verify_pending_teacher(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $teacher = User::factory()->create([
            'role' => 'teacher',
            'email_verified_at' => now(),
            'teacher_verified_at' => null,
        ]);

        $this->actingAs($admin);

        $response = $this->post("/fr/admin/teachers/{$teacher->id}/verify");

        $response->assertRedirect('/fr/admin');
        $this->assertNotNull($teacher->fresh()->teacher_verified_at);
    }

    public function test_verified_teacher_refreshing_pending_page_goes_to_teacher_space(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'email_verified_at' => now(),
            'teacher_verified_at' => now(),
        ]);

        $this->actingAs($teacher);

        $this->get('/fr/teacher-pending')->assertRedirect('/fr/teacher-space');
    }

    public function test_payment_history_page_loads_for_verified_user(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'email_verified_at' => now(),
            'teacher_verified_at' => now(),
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
            'subscription_tier' => 'premium',
            'premium_duration' => '1_month',
            'premium_level_key' => '1ere-bac::science-math',
            'premium_teacher_id' => $teacher->id,
            'premium_subject_key' => 'physics',
            'premium_expires_at' => now()->addMonth(),
        ]);

        $this->actingAs($student);

        $response = $this->get('/fr/payment-history');

        $response->assertOk();
        $response->assertSee('Historique des paiements');
        $response->assertSee(route('teacher.index.locale', ['locale' => 'fr']), false);
        $response->assertSee('Premium');
        $response->assertSee('1ere bac / science math');
        $response->assertSee($teacher->name);
        $response->assertSee('Physique');
    }

    public function test_payment_page_from_premium_video_shows_matching_level_pack_only(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'email_verified_at' => now(),
            'teacher_verified_at' => now(),
        ]);

        $response = $this->get("/fr/payment?teacher={$teacher->id}&subject=physics&level=2eme-bac::science-physique");

        $response->assertOk();
        $response->assertSee('Tarifs autres niveaux');
        $response->assertDontSee('Tarifs tronc commun');
        $response->assertSee(str_replace(' ', '+', $teacher->name), false);
    }

    public function test_admin_subscription_update_saves_teacher_and_subject_scope(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'email_verified_at' => now(),
            'teacher_verified_at' => now(),
        ]);
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin);

        $response = $this->post("/fr/admin/users/{$student->id}/subscription", [
            'subscription_tier' => 'premium',
            'premium_duration' => '1_month',
            'premium_level_key' => '2eme-bac::science-physique',
            'premium_teacher_id' => $teacher->id,
            'premium_subject_key' => 'physics',
        ]);

        $response->assertRedirect('/fr/admin');

        $student->refresh();
        $this->assertSame('premium', $student->subscription_tier);
        $this->assertSame($teacher->id, $student->premium_teacher_id);
        $this->assertSame('physics', $student->premium_subject_key);
    }

    public function test_admin_user_is_redirected_to_admin_page_after_login(): void
    {
        User::factory()->create([
            'email' => 'admin-login@example.com',
            'password' => 'secret123',
            'role' => 'admin',
            'preferred_locale' => 'fr',
        ]);

        $response = $this->post('/fr/login', [
            'email' => 'admin-login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/fr/admin');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'recover@example.com',
        ]);

        $token = Password::createToken($user);

        $response = $this->post('/fr/reset-password', [
            'token' => $token,
            'email' => 'recover@example.com',
            'password' => 'new-secret123',
            'password_confirmation' => 'new-secret123',
        ]);

        $response->assertRedirect('/fr/login');
        $this->assertCredentials([
            'email' => 'recover@example.com',
            'password' => 'new-secret123',
        ]);
    }

    public function test_course_page_loads(): void
    {
        $response = $this->get('/fr/course');

        $response->assertOk();
        $response->assertSee('Presentation du cours et demarrage rapide');
        $response->assertSee('Paiement par WhatsApp');
    }

    public function test_admin_page_loads(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Listing User',
            'email' => 'listing@example.com',
            'phone' => '0611111111',
            'role' => 'admin',
            'preferred_locale' => 'fr',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/fr/admin');

        $response->assertOk();
        $response->assertSee('Tableau de bord administrateur');
        $response->assertSee('listing@example.com');
        $response->assertSee('users');
    }

    public function test_student_cannot_access_admin_page(): void
    {
        $student = User::factory()->create([
            'email' => 'student-admin-block@example.com',
            'role' => 'student',
            'preferred_locale' => 'fr',
        ]);

        $this->actingAs($student);

        $response = $this->from('/fr/course')->get('/fr/admin');

        $response->assertRedirect('/fr/course');
        $response->assertSessionHasErrors([
            'email' => 'Acces reserve aux administrateurs.',
        ]);
    }
}
