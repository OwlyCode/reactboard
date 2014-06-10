<?php

namespace OwlyCode\ReactBoard\Application;

use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Exception\ApplicationNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationRepository
{
    /**
     * @var array
     */
    private $applications = array();

    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var OwlyCode\ReactBoard\Application\MainApplicationInterface
     */
    private $mainApplication;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function register(ApplicationInterface $application)
    {
        if ($application instanceof MainApplicationInterface) {
            if ($this->mainApplication) {
                throw new \RuntimeException('You cannot have two main applications.');
            } else {
                $this->mainApplication = $application;
            }
        }
        $this->applications[$application->getName()] = $application;
        $application->buildContainer();
        $this->dispatcher->dispatch('application.registered', new ApplicationEvent($application));
    }

    public function init()
    {
        foreach ($this->applications as $application) {
            $application->init();
        }
    }

    public function getArray()
    {
        return $this->applications;
    }

    public function getMainApplication()
    {
        return $this->mainApplication;
    }

    public function get($name)
    {
        if (!array_key_exists($name, $this->applications)) {
            throw new ApplicationNotFoundException(sprintf('The requested %s application was not registered in the ApplicationServer.', $name));
        }
        return $this->applications[$name];
    }
}
