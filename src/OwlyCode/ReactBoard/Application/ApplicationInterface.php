<?php

namespace OwlyCode\ReactBoard\Application;

use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\RequestInterface;
use OwlyCode\ReactBoard\Server\WebSocketServer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface ApplicationInterface {

    public function getName();

    public function getViewDir();

    public function getAssetsDir();

    public function setWebSocketServer(WebSocketServer $socketServer);

    public function setDispatcher(EventDispatcherInterface $dispatcher);

    public function execute($moduleName, Request $request);

    public function getJavascripts();

    public function getStylesheets();
}
