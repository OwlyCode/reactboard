<?php

namespace OwlyCode\ReactBoard\Application;

use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Exception\ApplicationNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationRepository
{
    private $applications = array();

    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function register(ApplicationInterface $application)
    {
        $this->applications[$application->getName()] = $application;
        $this->dispatcher->dispatch('application.registered', new ApplicationEvent($application));
    }

    public function getArray()
    {
        return $this->applications;
    }

    public function get($name)
    {
        if (!array_key_exists($name, $this->applications)) {
            throw new ApplicationNotFoundException(sprintf('The requested %s application was not registered in the ApplicationServer.', $name));
        }
        return $this->applications[$name];
    }
}
