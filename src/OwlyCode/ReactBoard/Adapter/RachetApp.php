<?php

namespace OwlyCode\ReactBoard\Adapter;

use Ratchet\ComponentInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Http\HttpServerInterface;
use Ratchet\Http\OriginCheck;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory as LoopFactory;
use React\EventLoop\LoopInterface;
use React\Socket\Server as Reactor;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RachetApp {
    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    public $routes;

    /**
     * @var \Ratchet\Server\IoServer
     */
    protected $server;

    /**
     * The Host passed in construct used for same origin policy
     * @var string
     */
    protected $httpHost;

    /**
     * @var int
     */
    protected $routeCounter = 0;

    /**
     * @param string        $httpHost HTTP hostname clients intend to connect to. MUST match JS `new WebSocket('ws://$httpHost');`
     * @param int           $port     Port to listen on.
     * @param string        $address  IP address to bind to. Default is localhost/proxy only. '0.0.0.0' for any machine.
     */
    public function __construct($httpHost = 'localhost', $port = 8080, $address = '127.0.0.1') {
        $loop = LoopFactory::create();

        $this->httpHost = $httpHost;

        $socket = new Reactor($loop);
        $socket->listen($port, $address);

        $this->routes  = new RouteCollection;
        $this->server = new IoServer(new HttpServer(new RachetRouter(new UrlMatcher($this->routes, new RequestContext))), $socket, $loop);
    }

    public function route($path, $controller, $requirements = array()) {
        if ($controller instanceof MessageComponentInterface) {
            $decorated = new WsServer($controller);
        } else {
            $decorated = $controller;
        }

        $this->routes->add('rr-' . ++$this->routeCounter, new Route($path, array('_controller' => $decorated), $requirements, array(), $this->httpHost));

        return $decorated;
    }

    /**
     * Run the server by entering the event loop
     */
    public function run() {
        $this->server->run();
    }
}
