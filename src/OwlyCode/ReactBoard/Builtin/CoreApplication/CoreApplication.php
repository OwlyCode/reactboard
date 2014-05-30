<?php

namespace OwlyCode\ReactBoard\Builtin\CoreApplication;

use Guzzle\Http\Message\Response;
use OwlyCode\ReactBoard\Application\AbstractApplication;
use OwlyCode\ReactBoard\Application\ApplicationInterface;

class CoreApplication extends AbstractApplication implements ApplicationInterface
{
    public function __construct($defaultAppName, $defaultModule)
    {
        $this->module('landing', function() use ($defaultAppName, $defaultModule) {
            return $this->render('landing.html.twig', array('default' => array('app' => $defaultAppName, 'module' => $defaultModule)));
        });

        $instance = $this;

        $this->module('command', function($request) use ($instance) {
            $application = $request->getQuery()->get('app');
            $module = $request->getQuery()->get('module');

            $instance->getWebSocketServer()->switchApp($application, $module);
            return new Response('');
        });
    }

    public function autoloadAssets(array $js, array $css)
    {
        $this->getTemplateEngine()->addGlobal('javascripts', $js);
        $this->getTemplateEngine()->addGlobal('stylesheets', $css);
    }

    public function getName()
    {
        return 'home';
    }

    public function getViewdir()
    {
        return __DIR__ . '/views';
    }

    public function getAssetsDir()
    {
        return __DIR__ . '/assets';
    }

    public function getJavascripts()
    {
        return array('js/main.js', 'js/websocket.jquery.js');
    }

    public function getStylesheets()
    {
        return array('css/main.css');
    }
}
