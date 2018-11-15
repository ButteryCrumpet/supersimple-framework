<?php

namespace SuperSimpleFramework;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use SuperSimpleKernel\EmitterInterface;
use SuperSimpleKernel\Kernel;
use SuperSimpleKernel\RouterInterface;

class App
{
    private $container;
    private $kernel;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = is_null($container) ? new AppContainer() : $container;
        $this->kernel = new Kernel(
            $this->container->get(RouterInterface::class),
            $this->container->get(EmitterInterface::class)
        );
    }

    public function run(ServerRequestInterface $request = null)
    {
        $request = is_null($request) ? ServerRequest::fromGlobals() : $request;
        $this->kernel->run($request);
    }

    public function register($id, $value)
    {
        $this->container->register($id, $value);
    }

    public function extract($name)
    {
        return $this->container->get($name);
    }


    public function router()
    {
        return $this->container->get(RouterInterface::class);
    }
}