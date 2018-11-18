<?php

namespace SuperSimpleFramework\Wrappers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ClosureWrapper implements MiddlewareInterface
{
    private $closure;
    private $args;

    public function __construct(\Closure $closure, array $args)
    {
        $this->closure = $closure;
        $this->args = array_values($args);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return call_user_func_array($this->closure, array_merge([$request], $this->args));
    }
}