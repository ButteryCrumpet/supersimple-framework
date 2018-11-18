<?php

use PHPUnit\Framework\TestCase;
use SuperSimpleFramework\HandlerFactory;

class HandlerFactoryTest extends TestCase
{
    public function testItInitializes()
    {
        $resolver = $this->createMock(\SuperSimpleFramework\Interfaces\ResolverInterface::class);
        $this->assertInstanceOf(
            HandlerFactory::class,
            new HandlerFactory($resolver)
        );
    }

    public function testItReturnsRequestHandlerInterface()
    {
        $resolver = $this->createMock(\SuperSimpleFramework\Interfaces\ResolverInterface::class);
        $resolver->method("resolve")
            ->willReturn($this->createMock(stdClass::class));
        $middleware = $this->createMock(\Psr\Http\Server\MiddlewareInterface::class);
        $handlerFactory = new HandlerFactory($resolver);
        $this->assertInstanceOf(
            \Psr\Http\Server\RequestHandlerInterface::class,
            $handlerFactory->make("handler:ho", [], [$middleware]),
            "With string handler"
        );
        $closure = function () {};
        $this->assertInstanceOf(
            \Psr\Http\Server\RequestHandlerInterface::class,
            $handlerFactory->make($closure, [], [$middleware]),
            "With closure handler"
        );
    }

    public function testItThrowsOnNonMiddleware()
    {
        $this->expectException(\InvalidArgumentException::class);
        $resolver = $this->createMock(\SuperSimpleFramework\Interfaces\ResolverInterface::class);
        $resolver->method("resolve")
            ->willReturn($this->createMock(stdClass::class));
        $handlerFactory = new HandlerFactory($resolver);
        $this->assertInstanceOf(
            \Psr\Http\Server\RequestHandlerInterface::class,
            $handlerFactory->make("handler:ho", [], ["not-middleware"])
        );
    }

    public function testItThrowsOnUnresolvableHandler()
    {
        $this->expectException(\InvalidArgumentException::class);
        $resolver = $this->createMock(\SuperSimpleFramework\Interfaces\ResolverInterface::class);
        $handlerFactory = new HandlerFactory($resolver);
        $this->assertInstanceOf(
            \Psr\Http\Server\RequestHandlerInterface::class,
            $handlerFactory->make([], [], [])
        );
    }
}