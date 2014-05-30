<?php

namespace OwlyCode\ReactBoard\Application;

use Guzzle\Http\Message\Request;
use OwlyCode\ReactBoard\Server\WebSocketServer;

interface ApplicationInterface {

    public function getName();

    public function getViewDir();

    public function getAssetsDir();

    public function setWebSocketServer(WebSocketServer $socketServer);

    public function execute($moduleName, Request $request);

    public function getJavascripts();

    public function getStylesheets();
}
