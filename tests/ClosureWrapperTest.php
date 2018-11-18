<?php

use PHPUnit\Framework\TestCase;
use SuperSimpleFramework\Wrappers\ClosureWrapper;

class ClosureWrapperTest extends TestCase
{
    public function testItInitializes()
    {
        $closure = function () {};
        $this->assertInstanceOf(
              ClosureWrapper::class,
            new ClosureWrapper($closure, [])
        );
    }

    public function testItPassesArgsCorrectly()
    {
        $in_request = $this->createMock(\Psr\Http\Message\ServerRequestInterface::class);
        $closure = function ($request, $id, $name) use ($in_request) {
            $this->assertEquals($in_request, $request);
            $this->assertEquals("2", $id);
            $this->assertEquals("name", $name);
            return $this->createMock(\Psr\Http\Message\ResponseInterface::class);
        };
        $wrapped = new ClosureWrapper($closure, ["id" => "2", "name" => "name"]);
        $handler = $this->createMock(\Psr\Http\Server\RequestHandlerInterface::class);
        $wrapped->process($in_request, $handler);
    }
}