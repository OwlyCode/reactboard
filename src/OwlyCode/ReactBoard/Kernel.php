<?php

namespace OwlyCode\ReactBoard;

use OwlyCode\ReactBoard\Adapter\RachetApp;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Application\ApplicationRepository;
use OwlyCode\ReactBoard\Server;
use Ratchet\App;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing\Route;

class Kernel
{
    /**
     * @var OwlyCode\ReactBoard\Server\WebSocketServer
     */
    private $socketServer;

    /**
     * @var OwlyCode\ReactBoard\Server\ApplicationServer
     */
    private $applicationServer;

    /**
     * @var OwlyCode\ReactBoard\Server\AssetServer
     */
    private $assetServer;

    /**
     * @var OwlyCode\ReactBoard\Application\ApplicationRepository
     */
    private $applications;

    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    private $hostname;

    private $port;

    public function __construct($hostname = 'localhost', $port = 8080)
    {
        $this->hostname = $hostname;
        $this->port = $port;

        $this->dispatcher = new EventDispatcher();
        $this->applications = new ApplicationRepository($this->dispatcher);
        $this->socketServer = new Server\WebSocketServer($this->dispatcher);
        $this->applicationServer = new Server\ApplicationServer($this->dispatcher, $this->applications);
        $this->assetServer = new Server\AssetServer($this->dispatcher, $this->applications);
    }

    public function registerApplication(ApplicationInterface $application)
    {
        $this->applications->register($application);
    }

    public function run()
    {
        $app = new RachetApp($this->hostname, $this->port, '0.0.0.0');
        $app->route('/ws', $this->socketServer);
        $app->route('/{application}/{module}', $this->applicationServer);
        $app->route('/public/{application}/{asset}', $this->assetServer, array('asset' => '.*'));

        $this->dispatcher->dispatch('reactboard.start', new Event());
        $app->run();
    }
}
