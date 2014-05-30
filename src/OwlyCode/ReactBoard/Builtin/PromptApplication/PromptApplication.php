<?php

namespace OwlyCode\ReactBoard\Builtin\PromptApplication;

use Guzzle\Http\Message\RequestInterface;
use OwlyCode\ReactBoard\Application\AbstractApplication;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Application\InteractionEvent;

class PromptApplication extends AbstractApplication implements ApplicationInterface
{
    private $message;

    public function init()
    {
        $this->watch('prompt.state.activate', array($this, 'onActivate'));
        $this->watch('prompt.request.prompt', array($this, 'onApp'));
    }

    public function onApp(RequestInterface $request)
    {
        return $this->render('prompt.html.twig', array('message' => $this->message));
    }

    public function onActivate(RequestInterface $request)
    {
        $this->message = $request->getQuery()->get('message');
    }

    public function getName()
    {
        return 'prompt';
    }

    public function getViewDir()
    {
        return __DIR__ . '/views';
    }

    public function getAssetsDir()
    {
        return __DIR__ . '/assets';
    }

    public function getStylesheets()
    {
        return array('main.css');
    }
}
