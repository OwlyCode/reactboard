<?php

namespace OwlyCode\ReactBoard\Server;

use OwlyCode\ReactBoard\Application\ApplicationEvent;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use Ratchet\App;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WebSocketServer implements MessageComponentInterface {
    protected $clients;

    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->clients = new \SplObjectStorage;

        $this->dispatcher->addListener('application.registered', array($this, 'onApplicationRegistered'));
    }

    public function onApplicationRegistered(ApplicationEvent $event)
    {
        $event->getApplication()->setWebSocketServer($this);
    }

    public function switchApp($appName, $moduleName)
    {
        foreach ($this->clients as $client) {
            $client->send(str_replace('\\/', '/', json_encode(array('type' => 'switch', 'url' => '/'.$appName.'/'.$moduleName))));
        }
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {

    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}
