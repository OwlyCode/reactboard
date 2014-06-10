<?php

namespace OwlyCode\ReactBoard;

use OwlyCode\ReactBoard\Adapter\RachetApp;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Asset\AssetInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\EventDispatcher\Event;

class Kernel
{
    private $container;

    private $hostname;

    private $port;

    private $rootDir;

    public function __construct($hostname = 'localhost', $port = 8080)
    {
        $this->hostname  = $hostname;
        $this->port      = $port;

        $this->container = new ContainerBuilder();
        $loader          = new XmlFileLoader($this->container, new FileLocator(__DIR__));
        $loader->load('Resources/services.xml');
        $this->container->setParameter('kernel.root_dir', $this->getRootDir());
    }

    public function configureContainer(callable $callback)
    {
        call_user_func($callback, $this->container);
    }

    public function register(ApplicationInterface $application)
    {
        $application->setContainer($this->container);
        $this->container->get('application_repository')->register($application);
    }

    public function link(AssetInterface $asset)
    {
        $this->container->get('assets_repository')->add($asset);
    }

    public function run()
    {
        $this->container->get('application_repository')->init();
        $app = new RachetApp($this->hostname, $this->port, '0.0.0.0');
        $app->route('/ws', $this->container->get('server.web_socket'));
        $app->route('/{application}/{module}', $this->container->get('server.application'));
        $app->route('/public/{asset}', $this->container->get('server.assets'), array('asset' => '.*'));

        $this->container->get('event_dispatcher')->dispatch('reactboard.start', new Event());
        $app->run();
    }

    /**
     * From Symfony2's Kernel implementation
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $r = new \ReflectionObject($this);
            $this->rootDir = str_replace('\\', '/', dirname($r->getFileName()));
        }

        return $this->rootDir;
    }
}
