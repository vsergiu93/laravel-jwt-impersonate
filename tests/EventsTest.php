<?php

namespace Rickycezar\Tests;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Rickycezar\Impersonate\Events\LeaveImpersonation;
use Rickycezar\Impersonate\Events\TakeImpersonation;
use Rickycezar\Tests\Stubs\Models\User;

class EventsTest extends TestCase
{
    /** @var  User */
    protected $admin;

    /** @var  User */
    protected $user;

    public function setUp():void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
    }

    /** @test */
    public function it_dispatches_events_when_taking_impersonation()
    {
        Event::fake();

        $admin = $this->admin;
        $user = $this->user;

        $this->assertTrue($admin->impersonate($user));

        Event::assertDispatched(TakeImpersonation::class, function ($event) use ($admin, $user) {
            return $event->impersonator->id == $admin->id && $event->impersonated->id == $user->id;
        });

        Event::assertNotDispatched(Login::class);
    }

    /** @test */
    public function it_dispatches_events_when_leaving_impersonation()
    {
        Event::fake();

        $admin = $this->admin;
        $user = $this->user;

        $this->assertTrue($admin->impersonate($user));
        $this->assertTrue($user->leaveImpersonation());

        Event::assertDispatched(LeaveImpersonation::class, function ($event) use ($admin, $user) {
            return $event->impersonator->id == $admin->id && $event->impersonated->id == $user->id;
        });

        Event::assertNotDispatched(Logout::class);
    }
}
