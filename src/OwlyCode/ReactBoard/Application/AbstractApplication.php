<?php

namespace OwlyCode\ReactBoard\Application;

use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\RequestInterface;
use OwlyCode\ReactBoard\Application\InteractionEvent;
use OwlyCode\ReactBoard\Server\WebSocketServer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AbstractApplication
{
    /**
     * @var OwlyCode\ReactBoard\Server\WebSocketServer
     */
    private $socketServer;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    public function init()
    {

    }

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function setWebSocketServer(WebSocketServer $socketServer)
    {
        $this->socketServer = $socketServer;
    }

    public function getWebSocketServer()
    {
        return $this->socketServer;
    }

    public function getTemplateEngine()
    {
        if(!$this->twig) {
            $loader = new \Twig_Loader_Filesystem($this->getViewDir());
            $this->twig = new \Twig_Environment($loader);
        }

        return $this->twig;
    }

    public function render($template, $options = array())
    {
        return $this->getTemplateEngine()->render($template, $options);
    }

    public function getViewDir()
    {
        return '.';
    }

    public function getAssetsDir()
    {
        return '.';
    }

    public function getJavascripts()
    {
        return array();
    }

    public function getStylesheets()
    {
        return array();
    }

    public function watch($event, callable $callback)
    {
        $this->dispatcher->addListener($event, function(InteractionEvent $event) use ($callback) {
            $event->stopPropagation();
            $event->setResult(call_user_func($callback, $event->getRequest()));
        });
    }

    public function execute($moduleName, Request $request)
    {
        $event = new InteractionEvent($request);
        $this->dispatcher->dispatch($this->getName() . '.request.' . $moduleName, $event);

        return $event->getResult();
    }
}
