<?php

namespace OwlyCode\ReactBoard\Application;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use OwlyCode\ReactBoard\Application\InteractionEvent;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AbstractApplication
{
    private $viewDir;

    protected $container;

    public function getName()
    {
        return 'default';
    }

    public function buildContainer()
    {

    }

    public function init()
    {

    }

    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function get($service)
    {
        return $this->container->get($service);
    }

    public function render($template, $options = array())
    {
        return new Response(200, [], $this->container->get('twig')->render($this->getViewDir(). DIRECTORY_SEPARATOR . $template, $options));
    }

    public function getViewDir()
    {
        if (null === $this->viewDir) {
            $r = new \ReflectionObject($this);
            $kernelParentDir = dirname($this->container->getParameter('kernel.root_dir')) . DIRECTORY_SEPARATOR;
            $this->viewDir = str_replace('\\', '/', dirname($r->getFileName()));
            $this->viewDir = str_replace($kernelParentDir, '', $this->viewDir) . DIRECTORY_SEPARATOR . 'views';
        }

        return $this->viewDir;
    }

    public function watch($event, callable $callback)
    {
        $this->get('event_dispatcher')->addListener($event, function(InteractionEvent $event) use ($callback) {
            $event->stopPropagation();
            $event->setResult(call_user_func($callback, $event->getRequest()));
        });
    }

    public function execute($moduleName, RequestInterface $request)
    {
        $event = new InteractionEvent($request);
        $this->get('event_dispatcher')->dispatch($this->getName() . '.request.' . $moduleName, $event);

        return $event->getResult();
    }
}
