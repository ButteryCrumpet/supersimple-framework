<?php

namespace SuperSimpleFramework\Interfaces;

interface RouteBuilderInterface
{
    public function get($path, $handler);

    public function post($path, $handler);

    public function any($path, $handler);

    public function route(array $methods, $path, $handler);

    public function group($path, \Closure $callback);

    public function with($middleware);
}