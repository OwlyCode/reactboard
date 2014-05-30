<?php

namespace OwlyCode\ReactBoard\Builtin\HelloApplication;

use OwlyCode\ReactBoard\Application\AbstractApplication;
use OwlyCode\ReactBoard\Application\ApplicationInterface;

class HelloApplication extends AbstractApplication implements ApplicationInterface
{
    public function __construct()
    {
        $this->module('world', function(){
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
}
