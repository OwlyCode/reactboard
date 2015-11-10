<?php

namespace OwlyCode\ReactBoard\Builtin\GithubApplication;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use OwlyCode\ReactBoard\Application\AbstractApplication;
use OwlyCode\ReactBoard\Application\ApplicationInterface;
use OwlyCode\ReactBoard\Asset\Asset;
use OwlyCode\ReactBoard\Exception\ApplicationInitializationException;

class GithubApplication extends AbstractApplication implements ApplicationInterface
{
    private $owner;
    private $repository;
    private $githubToken;

    public function __construct($githubToken)
    {
        $this->githubToken = $githubToken;
    }

    public function getName()
    {
        return 'github';
    }

    public function init()
    {
        $this->watch('github.state.activate', array($this, 'onActivate'));
        $this->watch('github.request.index', array($this, 'onIndex'));
        $this->watch('github.request.feed', array($this, 'onFeed'));
    }

    public function buildContainer()
    {
        $this->get('assets_repository')
            ->add(new Asset($this, __DIR__ . DIRECTORY_SEPARATOR . 'assets', 'main.js'))
        ;
    }

    public function onActivate(RequestInterface $request)
    {
        $this->repository = $request->getQuery()->get('repository');
        $this->owner      = $request->getQuery()->get('owner');

        if (!$this->repository || !$this->owner) {
            throw new ApplicationInitializationException('No owner and/or repository currently defined, you must provide them.');
        }
    }

    public function onIndex(RequestInterface $request)
    {
        return $this->render('index.html.twig', array(
            'repository' => $this->repository,
            'owner'      => $this->owner
        ));
    }

    public function onFeed(RequestInterface $request)
    {
        $client = new \Github\Client();
        $client->authenticate($this->githubToken, \Github\Client::AUTH_HTTP_TOKEN);
        $prs = $client->api('pull_request')->all($this->owner, $this->repository, array('state' => 'open'));

        foreach ($prs as $key => $pr) {
            $prs[$key]['status'] = $client->api('repository')->statuses()->combined($this->owner, $this->repository, $pr['head']['sha']);
        }

        return new Response(200, array('Content-Type' => 'application/json'), json_encode($prs));
    }
}
