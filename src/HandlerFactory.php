<?php

namespace SuperSimpleFramework;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SuperSimpleDIResolver\ResolverInterface;
use SuperSimpleRequestHandler\Handler;
use SuperSimpleRouting\HandlerFactoryInterface;

class HandlerFactory implements HandlerFactoryInterface
{
    private $resolver;

    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    public function make($handler, array $args, array $middleware): RequestHandlerInterface
    {
        $resolvedMiddleware = array_map(function ($middleware) {
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
        }, $middleware);
        $resolvedMiddleware[] = $this->getController($handler, $args);

        return new Handler($resolvedMiddleware);
    }

    private function getController($handler, $args)
    {
        if ($handler instanceof \Closure) {
            return new ClosureWrapper($handler, $args);
        } elseif (is_string($handler)) {
            $split = explode(":", $handler);
            $resolved = $this->resolver->resolve($split[0]);
            $method = isset($split[1]) ? $split[1] : "__invoke";
            return new MethodWrapper($resolved, $method, $args);
        }
        throw new \InvalidArgumentException("A handler must be either a closure, and invokable class or a class:method string.");
    }
}