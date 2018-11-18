<?php

namespace SuperSimpleFramework;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MethodWrapper implements MiddlewareInterface
{
    private $class;
    private $method;
    private $args;

    public function __construct($class, $method, array $args)
    {
        $this->class = $class;
        $this->method = $method;
        $this->args = array_values($args);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->class instanceof RequestAwareInterface)
        {
            $this->class->setRequest($request);
        }
        return call_user_func_array([$this->class, $this->method], $this->args);
    }
}