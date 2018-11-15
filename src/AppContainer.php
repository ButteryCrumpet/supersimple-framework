<?php

namespace SuperSimpleFramework;

use GuzzleHttp\Psr7\Response;
use SuperSimpleDI\Container;
use SuperSimpleDIResolver\Resolver;
use SuperSimpleDIResolver\ResolverInterface;
use SuperSimpleKernel\EmitterInterface;
use SuperSimpleKernel\RouterInterface;
use SuperSimpleRouting\HandlerFactoryInterface;
use SuperSimpleRouting\Route;

class AppContainer extends Container
{
    public function __construct()
    {
        $this->register(HandlerFactoryInterface::class, function (Container $c) {
            return new HandlerFactory($c->get(ResolverInterface::class));
        });

        $this->register(ResolverInterface::class, function(Container $c) {
            return new Resolver($c);
        });

        $this->register(EmitterInterface::class, function () {
            return new Emitter();
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
    }
}