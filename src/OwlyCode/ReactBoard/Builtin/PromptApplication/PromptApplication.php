<?php

namespace OwlyCode\ReactBoard\Builtin\PromptApplication;

use Guzzle\Http\Message\RequestInterface;
use OwlyCode\ReactBoard\Application\AbstractApplication;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Application\InteractionEvent;
use OwlyCode\ReactBoard\Asset\Asset;

class PromptApplication extends AbstractApplication implements ApplicationInterface
{
    private $message;

    public function buildContainer()
    {
        $this->get('assets_repository')->add(new Asset($this, __DIR__ . DIRECTORY_SEPARATOR . 'assets', 'main.css'));
    }

    public function init()
    {
        $this->watch('prompt.state.activate', array($this, 'onActivate'));
        $this->watch('prompt.request.index', array($this, 'onIndex'));
    }

    public function onIndex(RequestInterface $request)
    {
        return $this->render('index.html.twig', array('message' => $this->message));
    }

    public function onActivate(RequestInterface $request)
    {
        $this->message = $request->getQuery()->get('message');
    }

    public function getName()
    {
        return 'prompt';
    }
}
