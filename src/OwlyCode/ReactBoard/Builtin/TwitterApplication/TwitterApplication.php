<?php

namespace OwlyCode\ReactBoard\Builtin\TwitterApplication;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use OwlyCode\ReactBoard\Application\AbstractApplication;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Exception\ApplicationInitializationException;

class TwitterApplication extends AbstractApplication implements ApplicationInterface
{
    private $hashtag;

    public function __construct($defaultHashtag = '')
    {
        $this->setHashtag($defaultHashtag);
    }

    public function buildContainer()
    {
        $this->container->register('twitter', '\Twitter')
            ->addArgument('%twitter.consumer_key%')
            ->addArgument('%twitter.consumer_secret%')
            ->addArgument('%twitter.access_token%')
            ->addArgument('%twitter.access_token_secret%')
        ;
    }

    public function init()
    {
        $this->watch('twitter.state.activate', array($this, 'onActivate'));
        $this->watch('twitter.request.index', array($this, 'onIndex'));
        $this->watch('twitter.request.feed', array($this, 'onFeed'));
    }

    public function onActivate(RequestInterface $request)
    {
        if ($hashtag = $request->getQuery()->get('hashtag')) {
            $this->setHashtag($hashtag);
        }

        if (!$this->hashtag) {
            throw new ApplicationInitializationException('No hashtag currently defined, you must provide a hashtag parameter.');
        }
    }

    public function onIndex(RequestInterface $request)
    {
        return $this->render('index.html.twig', array('hashtag' => $this->hashtag));
    }

    public function onFeed(RequestInterface $request)
    {
        $statuses = $this->get('twitter')->search(array(
            'q' => $this->hashtag,
            'since_id' => $request->getQuery()->get('sinceId'),
            'count' => 10
        ));

        return new Response(200, array('Content-Type' => 'application/json'), json_encode($statuses));
    }

    public function getName()
    {
        return 'twitter';
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

    public function getJavascripts()
    {
        return array('main.js');
    }

    protected function setHashtag($hashtag) {
        if($hashtag && $hashtag[0] != '#') {
            $this->hashtag = '#'.$hashtag;
        } else {
            $this->hashtag = $hashtag;
        }
    }
}
