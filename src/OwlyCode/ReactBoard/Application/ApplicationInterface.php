<?php

namespace OwlyCode\ReactBoard\Application;

use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\RequestInterface;
use OwlyCode\ReactBoard\Server\WebSocketServer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface ApplicationInterface {
    public function buildContainer();

    public function init();

    public function getName();

    public function getViewDir();

    public function setWebSocketServer(WebSocketServer $socketServer);

    public function execute($moduleName, Request $request);

    public function setContainer(ContainerBuilder $container);
}
