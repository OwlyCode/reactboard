<?php

namespace OwlyCode\ReactBoard\Application;

use Guzzle\Http\Message\RequestInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ApplicationInterface {
    /**
     * Called just after the application is registered. Use it to configure the DI container.
     */
    public function buildContainer();

    /**
     * Called just before the socket server starts.
     */
    public function init();

    public function getName();

    /**
     * Will be called when this application is requested to display a view. Have a look at AbstractApplication to
     * see an implementation based on events you can directly inherit.
     */
    public function execute($moduleName, RequestInterface $request);

    /**
     * Used to set the DI container for the application by the Kernel.
     */
    public function setContainer(ContainerBuilder $container);
}
