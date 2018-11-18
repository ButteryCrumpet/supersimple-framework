<?php

namespace SuperSimpleFramework\Traits;

use Psr\Http\Message\ServerRequestInterface;

trait RequestAware
{
    private $request;

    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
}