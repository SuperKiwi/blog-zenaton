<?php

namespace App\Zenaton\Events;

use Zenaton\Interfaces\EventInterface;

class PostModeratedEvent implements EventInterface
{
    public $decision;

    public function __construct(bool $decision)
    {
        $this->decision = $decision;
    }
}
