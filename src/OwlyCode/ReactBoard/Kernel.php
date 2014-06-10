<?php

namespace OwlyCode\ReactBoard;

use OwlyCode\ReactBoard\Adapter\RachetApp;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Application\ApplicationRepository;
use OwlyCode\ReactBoard\Server;
use Ratchet\App;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
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

    private $container;

    private $hostname;

    private $port;

    public function __construct($hostname = 'localhost', $port = 8080)
    {
        $this->hostname  = $hostname;
        $this->port      = $port;

        $this->container = new ContainerBuilder();
        $loader          = new XmlFileLoader($this->container, new FileLocator(__DIR__));
        $loader->load('Resources/services.xml');

        $this->dispatcher        = $this->container->get('event_dispatcher');
        $this->applications      = $this->container->get('application_repository');
        $this->socketServer      = $this->container->get('server.web_socket');
        $this->applicationServer = $this->container->get('server.application');
        $this->assetServer       = $this->container->get('server.assets');
    }

    public function configureContainer(callable $callback)
    {
        call_user_func($callback, $this->container);
    }

    public function registerApplication(ApplicationInterface $application)
    {
        $application->setContainer($this->container);
        $this->applications->register($application);
    }

    public function run()
    {
        $this->applications->init();
        $app = new RachetApp($this->hostname, $this->port, '0.0.0.0');
        $app->route('/ws', $this->socketServer);
        $app->route('/{application}/{module}', $this->applicationServer);
        $app->route('/public/{application}/{asset}', $this->assetServer, array('asset' => '.*'));

        $this->dispatcher->dispatch('reactboard.start', new Event());
        $app->run();
    }
}
