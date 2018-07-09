<?php

namespace Framework\Zenaton\Events;

use Zenaton\Interfaces\EventInterface;

class PostModeratedEvent implements EventInterface
{
    public $decision;

    public function __construct(bool $decision)
    {
        $this->decision = $decision;
    }
}
