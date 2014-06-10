<?php

namespace OwlyCode\ReactBoard\Builtin\CoreApplication;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use OwlyCode\ReactBoard\Application\AbstractApplication;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Application\ApplicationRepository;
use OwlyCode\ReactBoard\Application\InteractionEvent;
use OwlyCode\ReactBoard\Application\MainApplicationInterface;
use OwlyCode\ReactBoard\Exception\ApplicationInitializationException;

class CoreApplication extends AbstractApplication implements MainApplicationInterface
{
    private $applications;

    private $currentApplication;

    private $currentModule;

    private $defaultAppName;

    private $defaultModule;

    public function __construct($defaultAppName, $defaultModule)
    {
        $this->defaultAppName = $defaultAppName;
        $this->defaultModule = $defaultModule;
    }

    public function init()
    {
        $this->watch('home.request.landing', array($this, 'onLanding'));
        $this->watch('home.request.command', array($this, 'onCommand'));
    }

    public function onLanding(RequestInterface $request)
    {
        return $this->render('landing.html.twig', array('default' => array(
            'app' => $this->getCurrentApplication()->getName(),
            'module' => $this->getCurrentModule()
        )));
    }

    public function onCommand(RequestInterface $request)
    {
        $applicationName = $request->getQuery()->get('app');
        $module = $request->getQuery()->get('module');

        try {
            $application = $this->applications->get($applicationName);
            $this->get('event_dispatcher')->dispatch($application->getName() . '.state.activate', new InteractionEvent($request));
            $this->get('event_dispatcher')->dispatch($this->currentApplication->getName() . '.state.deactivate', new InteractionEvent($request));
            $this->getWebSocketServer()->switchApp($applicationName, $module);
            $this->currentApplication = $application;
            $this->currentModule = $module;

        } catch (ApplicationNotFoundException $e) {
            return $this->jsonResponse(404, 'This application does not exist.');
        } catch(ApplicationInitializationException $e) {
            return $this->jsonResponse(400, $e->getMessage());
        }

        return $this->jsonResponse(200, 'Application switched.');
    }

    public function getDefaultAppName()
    {
        return $this->defaultAppName;
    }

    public function getDefaultModule()
    {
        return $this->defaultModule;
    }

    public function getCurrentModule()
    {
        return $this->currentModule;
    }

    public function setCurrentModule($module)
    {
        $this->currentModule = $module;
    }

    public function getCurrentApplication()
    {
        return $this->currentApplication;
    }

    public function setCurrentApplication(ApplicationInterface $application)
    {
        $this->currentApplication = $application;
    }

    public function setApplications(ApplicationRepository $applications)
    {
        $this->applications = $applications;
    }

    public function registerAssets(array $js, array $css)
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

    protected function jsonResponse($status, $error)
    {
        return new Response($status, array('Content-Type' => 'application/json'), json_encode(array('message' => $error)));
    }
}
