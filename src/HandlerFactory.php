<?php

namespace SuperSimpleFramework;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SuperSimpleFramework\Interfaces\ResolverInterface;
use SuperSimpleFramework\Interfaces\RouteArgsAwareInterface;
use SuperSimpleFramework\Wrappers\ClosureWrapper;
use SuperSimpleFramework\Wrappers\MethodWrapper;
use SuperSimpleRequestHandler\Handler;
use SuperSimpleRouting\HandlerFactoryInterface;

class HandlerFactory implements HandlerFactoryInterface
{
    private $resolver;

    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public function make($handler, array $args, array $middleware): RequestHandlerInterface
    {
        $resolvedMiddleware = array_map(function ($middleware) use ($args) {
            $resolved = $this->resolveMiddleware($middleware);
           if ($resolved instanceof RouteArgsAwareInterface) {
               $resolved->setRouteArgs($args);
           }
           return $resolved;
        }, $middleware);
        $resolvedMiddleware[] = $this->wrapHandler($handler, $args);

        return new Handler($resolvedMiddleware);
    }

    private function wrapHandler($handler, $args)
    {
        if ($handler instanceof \Closure) {
            return new ClosureWrapper($handler, $args);
        } elseif (is_string($handler)) {
            $split = explode(":", $handler);
            $resolved = $this->resolver->resolve($split[0]);
            $method = isset($split[1]) ? $split[1] : "__invoke"; // eh
            return new MethodWrapper($resolved, $method, $args);
        }
        throw new \InvalidArgumentException("A handler must be either a closure, an invokable class or a class:method string.");
    }

    private function resolveMiddleware($middleware) {
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }
        $resolved = $this->resolver->resolve($middleware);
        if (!($resolved instanceof MiddlewareInterface)) {
            throw new \InvalidArgumentException(sprintf(
                "Middleware must be or resolve to Psr\Http\Server\MiddlewareInterface. %s was given",
                gettype($resolved) === "object" ? get_class($resolved) : gettype($resolved)
            ));
        }
        return $resolved;
    }
}