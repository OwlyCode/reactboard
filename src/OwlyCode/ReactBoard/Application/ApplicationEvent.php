<?php

namespace OwlyCode\ReactBoard\Application;

use Symfony\Component\EventDispatcher\Event;

class ApplicationEvent extends Event
{
    private $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }
}
