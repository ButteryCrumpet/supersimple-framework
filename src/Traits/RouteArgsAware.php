<?php

namespace SuperSimpleFramework\Traits;

trait RouteArgsAware
{
    private $routeArgs;

    public function setRouteArgs(array $args)
    {
        $this->routeArgs = $args;
    }
}