<?php

namespace SuperSimpleFramework;

use GuzzleHttp\Psr7\Response;
use SuperSimpleDI\Container;
use SuperSimpleKernel\EmitterInterface;
use SuperSimpleKernel\RouterInterface;
use SuperSimpleRouting\HandlerFactoryInterface;
use SuperSimpleRouting\Route;

class AppContainer extends Container
{
    public function __construct()
    {
        $this->register(HandlerFactoryInterface::class, function (Container $c) {
            return new HandlerFactory($c->get("resolver"));
        });

        $this->register("resolver", function(Container $c) {
            return new Resolver($c);
        });

        $this->register(EmitterInterface::class, function (Container $c) {
            return new Emitter($c->get("ChunkSize"));
        });

        $this->register(RouterInterface::class, function (Container $c) {
            return new Router(
                $c->get(HandlerFactoryInterface::class),
                $c->get('NotFoundRoute'),
                $c->get('NotAllowedRoute')
            );
        });

        $this->register("NotFoundRoute", function () {
           return new Route("GET", "/", function() {
              return new Response(404);
           });
        });

        $this->register("NotAllowedRoute", function () {
            return new Route("GET", "/", function() {
                return new Response(401);
            });
        });

        $this->register("ChunkSize", 4096);
    }
}