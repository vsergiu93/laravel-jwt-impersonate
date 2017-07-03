<?php

namespace Rickycezar\Impersonate\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TakeImpersonation
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var  Model */
    public $impersonator;

    /** @var  Model */
    public $impersonated;

    /**
     * Create a new event instance.
     *
     * @param Model $impersonator
     * @param Model $impersonated
     */
    public function __construct(Model $impersonator, Model $impersonated)
    {
        $this->impersonator = $impersonator;
        $this->impersonated = $impersonated;
    }
}
