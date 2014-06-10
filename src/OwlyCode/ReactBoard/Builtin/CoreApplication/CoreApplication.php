<?php

namespace OwlyCode\ReactBoard\Builtin\CoreApplication;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use OwlyCode\ReactBoard\Application\AbstractApplication;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Application\ApplicationRepository;
use OwlyCode\ReactBoard\Application\InteractionEvent;
use OwlyCode\ReactBoard\Application\MainApplicationInterface;
use OwlyCode\ReactBoard\Asset\Asset;
use OwlyCode\ReactBoard\Exception\ApplicationInitializationException;
use OwlyCode\ReactBoard\Exception\ApplicationNotFoundException;

class CoreApplication extends AbstractApplication implements MainApplicationInterface
{
    private $applications;

    private $currentApplication;

    private $currentModule;

    private $defaultAppName;

    private $defaultModule;

    private $theme;

    public function __construct($defaultAppName, $defaultModule = 'index', $theme = 'default')
    {
        $this->defaultAppName = $defaultAppName;
        $this->defaultModule  = $defaultModule;
        $this->theme          = $theme;
    }

    public function buildContainer()
    {
        $this->get('assets_repository')
            ->add(new Asset($this, __DIR__ . DIRECTORY_SEPARATOR . 'assets', 'js/websocket.jquery.js'))
            ->add(new Asset($this, __DIR__ . DIRECTORY_SEPARATOR . 'assets', 'js/main.js'))
            ->add(new Asset($this, __DIR__ . DIRECTORY_SEPARATOR . 'assets', 'css/main.css'))
        ;
    }

    public function init()
    {
        $this->watch('home.request.landing', array($this, 'onLanding'));
        $this->watch('home.request.command', array($this, 'onCommand'));
        $this->get('event_dispatcher')->addListener('reactboard.start', array($this, 'onReactBoardStart'));
    }

    public function onReactBoardStart()
    {
        $assetsRepository = $this->get('assets_repository');

        $this->get('twig')->addGlobal('assets', $assetsRepository->getAll());
        $this->get('twig')->addGlobal('theme', $this->theme);
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

        if (!$module) {
            $module = 'index';
        }

        try {
            $application = $this->applications->get($applicationName);
            $this->get('event_dispatcher')->dispatch($application->getName() . '.state.activate', new InteractionEvent($request));
            $this->get('event_dispatcher')->dispatch($this->currentApplication->getName() . '.state.deactivate', new InteractionEvent($request));
            $this->get('server.web_socket')->switchApp($applicationName, $module);
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

    public function getName()
    {
        return 'home';
    }

    protected function jsonResponse($status, $error)
    {
        return new Response($status, array('Content-Type' => 'application/json'), json_encode(array('message' => $error)));
    }
}
