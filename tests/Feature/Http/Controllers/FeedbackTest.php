<?php

namespace Tests\Feature\Http\Controllers;

use App\Mail\Feedback;
use App\User;
use Database\Seeders\RootSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
    }

    public function testShowRecipients__200()
    {
        $user = factory(User::class)->create(['enabled' => true]);

        $response = $this->actingAs($user)
                         ->get('/api/1/feedback/recipients');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'recipients' => config('app.feedback_recipients'),
                 ]);
    }

    public function testShowRecipients__302()
    {
        $response = $this->get('/api/1/feedback/recipients');

        $response->assertStatus(302);
    }

    public function testSend_200()
    {
        Mail::fake();

        /** @var User $user */
        $user = factory(User::class)->create(['enabled' => true]);

        $payload = [
            'message' => 'Lorem ipsum dolor sit amet'
        ];

        $this->actingAs($user)
             ->post('/api/1/feedback', $payload);

        Mail::assertSent(Feedback::class);

        Mail::assertSent(Feedback::class, function ($mail) {
            return $mail->hasTo(config('app.admin_email'));
        });
    }

    public function testSend_mailContent()
    {
        /** @var User $user */
        $user = factory(User::class)->create(['enabled' => true]);

        $message = 'Lorem ipsum dolor sit amet';
        $userAgent = 'Fake User Agent';

        $mailable = new Feedback($user, $message, $userAgent);

        $mailable->assertSeeInHtml(config('app.feedback_recipients'));
        $mailable->assertSeeInHtml($user->first_name);
        $mailable->assertSeeInHtml($user->last_name);
        $mailable->assertSeeInHtml($user->email);
        $mailable->assertSeeInHtml(config('app.name'));
        $mailable->assertSeeInHtml('Feedback');
        $mailable->assertSeeInHtml('Message');
        $mailable->assertSeeInHtml($message);

        $mailable->assertSeeInHtml('Meta');
        $mailable->assertSeeInHtml($userAgent);
        $mailable->assertSeeInHtml($user->lang);
        $mailable->assertSeeInHtml($user->managedBy->name);
    }
}
