<?php

namespace SuperSimpleFramework;

use Psr\Http\Message\ResponseInterface;
use SuperSimpleKernel\EmitterInterface;
use SuperSimpleResponseEmitter\Emitter as BaseEmitter;

class Emitter implements EmitterInterface
{
    private $emitter;

    public function __construct($chunkSize = 4096)
    {
        $this->emitter = new BaseEmitter($chunkSize);
    }

    public function emit(ResponseInterface $response)
    {
        $this->emitter->emit($response);
    }

}