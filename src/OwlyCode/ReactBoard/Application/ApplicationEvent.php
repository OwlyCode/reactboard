<?php

namespace OwlyCode\ReactBoard\Application;

use Symfony\Component\EventDispatcher\Event;

class ApplicationEvent extends Event
{
    /**
     * @var OwlyCode\ReactBoard\Application\ApplicationInterface
     */
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
