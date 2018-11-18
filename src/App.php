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
        $this->router = $this->container->get(RouterInterface::class);
        $this->kernel = new Kernel(
            $this->router,
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

    public function get($path, $handler)
    {
        return $this->router->get($path, $handler);
    }

    public function post($path, $handler)
    {
        return $this->router->post($path, $handler);
    }

    public function any($path, $handler)
    {
        return $this->router->any($path, $handler);
    }

    public function route($methods, $path, $handler)
    {
        return $this->router->route($methods, $path, $handler);
    }

    public function group($path, $callback)
    {
        return $this->router->group($path, $callback);
    }

    public function with($middleware)
    {
        return $this->router->with($middleware);
    }
}