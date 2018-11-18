<?php

use PHPUnit\Framework\TestCase;
use SuperSimpleFramework\Wrappers\MethodWrapper;

class MethodWrapperTest extends TestCase
{
    public function testItInitializes()
    {
        $obj = $this->createMock(\stdClass::class);
        $this->assertInstanceOf(
            MethodWrapper::class,
            new MethodWrapper($obj, "method", [])
        );
    }

    public function testItPassesArgsCorrectly()
    {
        $in_request = $this->createMock(\Psr\Http\Message\ServerRequestInterface::class);
        $obj = $this->createMock(TestRequestAwareHandler::class);
        $obj->method("genericMethod")
            ->willReturnCallback(function ($id, $name) use ($in_request) {
                $this->assertEquals("2", $id);
                $this->assertEquals("name", $name);
                return $this->createMock(\Psr\Http\Message\ResponseInterface::class);
            });
        $wrapped = new MethodWrapper($obj, "genericMethod", ["id" => "2", "name" => "name"]);
        $handler = $this->createMock(\Psr\Http\Server\RequestHandlerInterface::class);
        $wrapped->process($in_request, $handler);
    }

    public function testItPassesInRequestCorrectly()
    {
        $obj = $this->createMock(TestRequestAwareHandler::class);
        $obj->method("genericMethod")
            ->willReturn($this->createMock(\Psr\Http\Message\ResponseInterface::class));
        $request = $this->createMock(\Psr\Http\Message\ServerRequestInterface::class);
        $obj->expects($this->once())
            ->method("setRequest")
            ->with($this->equalTo($request));

        $wrapped = new MethodWrapper($obj, "genericMethod", ["id" => "2", "name" => "name"]);
        $handler = $this->createMock(\Psr\Http\Server\RequestHandlerInterface::class);
        $wrapped->process($request, $handler);
    }
}

interface TestRequestAwareHandler extends \SuperSimpleFramework\Interfaces\RequestAwareInterface
{
    public function genericMethod();
}