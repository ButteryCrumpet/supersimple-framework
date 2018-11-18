<?php

namespace SuperSimpleFramework;

use Psr\Http\Message\ServerRequestInterface;

trait RequestAwareTrait
{
    private $request;

    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
}