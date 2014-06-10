<?php

namespace OwlyCode\ReactBoard\Builtin\VlcApplication;

use Guzzle\Http\Message\RequestInterface;
use OwlyCode\ReactBoard\Application\AbstractApplication;
use OwlyCode\ReactBoard\Application\ApplicationInterface;

class VlcApplication extends AbstractApplication implements ApplicationInterface
{
    private $streamer;

    public function init()
    {
        $this->watch('vlc.state.activate', array($this, 'onActivate'));
        $this->watch('vlc.request.index', array($this, 'onIndex'));
    }

    public function onIndex(RequestInterface $request)
    {
        return $this->render('index.html.twig', array('streamer' => $this->streamer));
    }

    public function onActivate(RequestInterface $request)
    {
        $this->streamer = $request->getQuery()->get('streamer');
    }

    public function getName()
    {
        return 'vlc';
    }
}
