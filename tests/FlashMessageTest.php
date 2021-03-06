<?php

namespace EZ\LaravelMessages\Tests;

use EZ\FlashMessages\FlashService;
use Mockery;
use PHPUnit\Framework\TestCase;

class FlashMessageTest extends TestCase
{
    protected $session;

    protected $flash;

    public function setUp(): void
    {
        $this->session = Mockery::spy('EZ\FlashMessages\SessionStore');

        $this->flash = new FlashService($this->session);
    }

    /** @test */
    public function it_can_interact_with_a_message_as_an_array()
    {
        $this->flash->message('Test', 'one', 'two');

        $this->assertEquals('Test', $this->flash->messages[0]['message']);
    }

    /** @test */
    public function it_displays_default_flash_messages()
    {
        $this->flash->message('Test');

        $this->assertCount(1, $this->flash->messages);

        $message = $this->flash->messages[0];

        $this->assertEquals('', $message->title);
        $this->assertEquals('Test', $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertSessionIsFlashed();
    }

    /** @test */
    public function it_displays_multiple_flash_messages()
    {
        $this->flash->message('Test');
        $this->flash->message('Test 2');

        $this->assertCount(2, $this->flash->messages);

        $this->assertSessionIsFlashed(2);
    }

    /** @test */
    public function it_displays_success_flash_messages()
    {
        $this->flash->message('Test')->success();

        $message = $this->flash->messages[0];

        $this->assertEquals('', $message->title);
        $this->assertEquals('Test', $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertSessionIsFlashed();
    }

    /** @test */
    public function it_displays_warning_flash_messages()
    {
        $this->flash->message('Test')->warning();

        $message = $this->flash->messages[0];

        $this->assertEquals('', $message->title);
        $this->assertEquals('Test', $message->message);
        $this->assertEquals('warning', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertSessionIsFlashed();
    }

    /** @test */
    public function it_displays_error_flash_messages()
    {
        $this->flash->message('Test')->error();

        $message = $this->flash->messages[0];

        $this->assertEquals('', $message->title);
        $this->assertEquals('Test', $message->message);
        $this->assertEquals('danger', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertSessionIsFlashed();
    }

    /** @test */
    public function it_displays_important_flash_messages()
    {
        $this->flash->message('Test')->important();

        $message = $this->flash->messages[0];

        $this->assertEquals('', $message->title);
        $this->assertEquals('Test', $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(true, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertSessionIsFlashed();
    }

    /** @test */
    public function it_builds_an_overlay_flash_notification()
    {
        $this->flash->message('Thank You')->overlay();

        $message = $this->flash->messages[0];

        $this->assertEquals('Notice', $message->title);
        $this->assertEquals('Thank You', $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(true, $message->overlay);

        $this->flash->clear();

        $this->flash->overlay('Overlay message.', 'Overlay Title');

        $message = $this->flash->messages[0];

        $this->assertEquals('Overlay Title', $message->title);
        $this->assertEquals('Overlay message.', $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(true, $message->overlay);

        $this->assertSessionIsFlashed();
    }

    /** @test */
    public function it_clears_all_messages()
    {
        $this->flash->message('Test');

        $this->assertCount(1, $this->flash->messages);

        $this->flash->clear();

        $this->assertCount(0, $this->flash->messages);
    }

    /** @test */
    public function it_is_macroable()
    {
        $this->flash->macro('passthru', function ($message) {
            return $message;
        });

        $this->assertEquals('Macroable message', $this->flash->passthru('Macroable message'));
    }

    protected function assertSessionIsFlashed($times = 1)
    {
        $this->session
            ->shouldHaveReceived('flash')
            ->with('flash_notification', $this->flash->messages)
            ->times($times);
    }
}
