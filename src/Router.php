<?php

namespace SuperSimpleFramework;

use SuperSimpleFramework\Interfaces\RouteBuilderInterface;
use SuperSimpleKernel\RouterInterface;
use SuperSimpleRouting\Router as BaseRouter;

class Router extends BaseRouter implements RouteBuilderInterface, RouterInterface
{
}