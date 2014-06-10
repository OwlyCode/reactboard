<?php

namespace OwlyCode\ReactBoard\Builtin\HelloApplication;

use OwlyCode\ReactBoard\Application\AbstractApplication;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Asset\Asset;

class HelloApplication extends AbstractApplication implements ApplicationInterface
{

    public function buildContainer()
    {
        $this->get('assets_repository')->add(new Asset($this, __DIR__ . DIRECTORY_SEPARATOR . 'assets', 'main.css'));
    }

    public function init()
    {
        $this->watch('hello.request.index', function(){
            return $this->render('index.html.twig');
        });
    }

    public function getName()
    {
        return 'hello';
    }
}
