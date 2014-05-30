<?php

namespace OwlyCode\ReactBoard\Builtin\HelloApplication;

use OwlyCode\ReactBoard\Application\AbstractApplication;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Application\InteractionEvent;

class HelloApplication extends AbstractApplication implements ApplicationInterface
{
    public function init()
    {
        $this->watch('hello.request.world', function(){
            return $this->render('hello.html.twig');
        });
    }

    public function getName()
    {
        return 'hello';
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
