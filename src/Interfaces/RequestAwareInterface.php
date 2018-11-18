<?php

namespace SuperSimpleFramework;

use Psr\Http\Message\ServerRequestInterface;

interface RequestAwareInterface
{
    public function setRequest(ServerRequestInterface $request);
}