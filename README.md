ReactBoard
==========

What is it ?
------------

ReactBoard is an extensible dashboard based on React, Rachet and the Symfony Components. Install it on a server, set up the applications
you wish and plug a screen !

You can interact in real time with it via an http API.

Getting started
---------------

Create a composer.json

```json
{
    "autoload": {
        "psr-0": { "": "src/" }
    },
    "require": {
        "owlycode/reactboard": "~0.1"
    }
}
```

And then execute the following commands

```bash
composer install
cp vendor/owlycode/reactboard/app . -r
mv app/app.php.dist app/app.php
```

Now run the application with ``php app/app.php``

When browsing ``http://localhost:8080/home/landing``, you should see the welcome page.

Playing around
--------------

The following applications are included : hello, prompt, twitter and vlc.

- hello is the one you are seeing by default, it's basically an hello world.
- prompt will display a single message.
- twitter will display a live updated list of tweets from a hashtag.
- vlc will display a player (your browser needs the VLC plugin) streaming from a specified source.

You can switch between applications by issuing calls to the ``http://localhost:8080/home/command`` URL. Give a try to the following
commands and see what happens in your browser :

```bash
curl "http://localhost:8080/home/command?app=prompt&message=Oh%20Hi!"
```

Before running this one, you will need to setup a twitter application and enter its credentials in app/app.php

```bash
curl "http://localhost:8080/home/command?app=twitter&hashtag=symfony"
```

Before running this one, make sure you have vlc installed (tested on linux & windows). Don't forget to ``killall vlc`` when you are done.

```bash
vlc screen:// --screen-fps=20 --live-caching=10 --sout="#transcode{vcodec=mp2v,vb=256,fps=20,scale=Auto,acodec=none}:http{mux=raw,dst=:8081/}" -I dummy &
curl "http://localhost:8080/home/command?app=vlc&streamer=127.0.0.1:8081"
```

Customize the theme
-------------------

Create your own css file in ``app/Resources/css`` and replace the default theme in ``app/app.php`` :

```php
$kernel->link(new ExternalAsset(__DIR__ . '/Resources', 'css/your-theme-file.css'));
```

Create your own application
---------------------------

Create an ``src/MyNamespace/MyAppName`` folder with the following files :

```php
<?php
// src/MyNamespace/MyAppName/MyAppNameApplication.php

namespace MyNamespace\MyAppName;

use OwlyCode\ReactBoard\Application\AbstractApplication;
use OwlyCode\ReactBoard\Application\ApplicationInterface;

class MyAppNameApplication extends AbstractApplication implements ApplicationInterface
{
    /**
     * Called just before the socket server starts.
     */
    public function init()
    {
        $this->watch('my_app_name.request.index', function(){
            return $this->render('index.html.twig', array('status' => 'loaded'));
        });
    }

    public function getName()
    {
        return 'my_app_name';
    }
}
```

```twig
{# src/MyNamespace/MyAppName/views/index.html.twig #}

<h1>My application is {{ status }} ! :-)</h1>
```

Then register your newly created application in ``app/app.php``:

```php
$kernel->register(new MyAppNameApplication());
```

You can now start/restart ReactBoard, open your browser and try : ``curl "http://localhost:8080/home/command?app=my_app_name"``

Going further :
---------------

ReactBoard is still in early development and more documentation will be coming. If you have any question please feel free to open an issue :-)

Meanwhile, to see how to do more advanced stuff, like loading your own assets or access the dependency injection mechanism, take a look at the [builtin applications](https://github.com/OwlyCode/reactboard/tree/master/src/OwlyCode/ReactBoard/Builtin). The Twitter application is the most advanced and covers everything ReactBoard can do : application lifecycle events, live updates, dependency injection use, assets registering and many other things.
