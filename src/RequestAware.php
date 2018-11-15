<?php

namespace SuperSimpleFramework;

use Psr\Http\Message\ServerRequestInterface;

interface RequestAware
{
    public function setRequest(ServerRequestInterface $request);
}