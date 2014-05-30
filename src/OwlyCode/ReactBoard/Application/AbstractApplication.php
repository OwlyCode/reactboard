<?php

namespace OwlyCode\ReactBoard\Application;

use Guzzle\Http\Message\Request;
use OwlyCode\ReactBoard\Server\WebSocketServer;

class AbstractApplication
{
    private $modules = array();

    private $socketServer;

    private $twig;

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

    public function module($name, callable $callback)
    {
        $this->modules[$name] = $callback;
    }

    public function execute($moduleName, Request $request)
    {
        return $this->autorun($moduleName, $request, function(){
            return 'module not found.';
        });
    }

    public function autorun($moduleName, Request $request, callable $fallback = null)
    {
        if(isset($this->modules[$moduleName])) {
            return call_user_func($this->modules[$moduleName], $request);
        } else if ($fallback) {
            return call_user_func($fallback, $request);
        }
    }
}
