<?php

use PHPUnit\Framework\TestCase;
use SuperSimpleFramework\App;

class AppTest extends TestCase
{
    public function testItInitializes()
    {
        $this->assertInstanceOf(
            \SuperSimpleFramework\App::class,
            new App()
        );
    }

    public function testRoutesCorrectly()
    {
        $request = $this->createRequest($this->createUri("/post"));
        $response = $this->createResponse($this->createBody(), []);

        $app = new App();
        $router = $app->router();
        $router->get("/post", function (\Psr\Http\Message\ServerRequestInterface $r) use ($request, $response) {
           $this->assertEquals($request, $r);
           return $response;
        });
        $app->run($request);
    }

    public function testGroupRoutesCorrectlyWithClosure()
    {
        $request = $this->createRequest($this->createUri("/post/5"));
        $response = $this->createResponse($this->createBody(), []);

        $app = new App();
        $router = $app->router();
        $router->group("/{name}", function ($group) use ($request, $response) {
           $group->get("/{id}", function ($r, $name, $id) use ($request, $response) {
               $this->assertEquals($request, $r);
               $this->assertEquals("post", $name);
               $this->assertEquals("5", $id);
               return $response;
           });
        });
        $app->run($request);
    }

    public function testResolvesMiddleware()
    {
        $request = $this->createRequest($this->createUri("/post/5"));
        $response = $this->createResponse($this->createBody(), []);
        $middleware = $this->createMock(\Psr\Http\Server\MiddlewareInterface::class);
        $middleware->method("process")
            ->willReturnCallback(function($request, $handler) {
                $this->assertEquals("/post/5", $request->getUri()->getPath());
                $response = $handler->handle($request);
                return $this->createResponse($this->createBody("second body."), []);
            });

        $app = new App();
        $router = $app->router();
        $router->group("/{name}", function ($group) use ($request, $response) {
            $group->get("/{id}", function ($r, $name, $id) use ($request, $response) {
                $this->assertEquals($request, $r);
                $this->assertEquals("post", $name);
                $this->assertEquals("5", $id);
                return $response;
            })->with("seconding");
        });
        $app->register("seconding", $middleware);
        ob_start();
        $app->run($request);
        $output = ob_get_clean();
        $this->assertEquals("the\nsecond body.", $output);
    }

    public function testResolvesContainerServiceController()
    {
        $request = $this->createRequest($this->createUri("/post/5"));
        $response = $this->createResponse($this->createBody(), []);

        $app = new App();
        $app->register("service", function () use ($response) {
          return new TestHandler($this, $response);
        });
        $router = $app->router();
        $router->group("/{name}", function ($group) {
            $group->get("/{id}", "service:method");
        });
        $app->run($request);
    }

    public function testResolvesClassStringController()
    {
        $request = $this->createRequest($this->createUri("/post/5"));
        $response = $this->createResponse($this->createBody(), []);

        $app = new App();
        $app->register(TestHandler::class, function () use ($response) {
            return new TestHandler($this, $response);
        });
        $router = $app->router();
        $router->group("/{name}", function ($group) {
            $group->get("/{id}", TestHandler::class.":method");
        });
        $app->run($request);
    }

    public function testEmits()
    {
        $request = $this->createRequest($this->createUri("/post/5"));
        $response = $this->createResponse($this->createBody(), []);

        $app = new App();
        $router = $app->router();
        $router->group("/post", function ($group) use ($request, $response) {
            $group->get("/{id}", function () use ($request, $response) {
                return $response;
            });
        });
        ob_start();
        $app->run($request);
        $output = ob_get_clean();
        $this->assertEquals("the\nbody", $output);
    }

    private function createUri($path)
    {
        $uri = $this->createMock(\Psr\Http\Message\UriInterface::class);
        $uri->method("getPath")
            ->willReturn($path);
        return $uri;
    }

    private function createBody($b = null)
    {
        $b = is_null($b) ? "body" : $b;
        $body = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $body->method("isReadable")->willReturn(true);
        $body->method("isSeekable")->willReturn(true);
        $body->method("eof")->willReturnOnConsecutiveCalls(false, false, true);
        $body->method("read")->willReturnOnConsecutiveCalls("the\n", $b);
        return $body;
    }

    private function createRequest($uri)
    {
        $request = $this->createMock(\Psr\Http\Message\ServerRequestInterface::class);
        $request->method("getUri")
            ->willReturn($uri);
        $request->method("getMethod")
            ->willReturn("GET");
        return $request;
    }

    private function createResponse($body, $headers)
    {
        $response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
        $response->method("getStatusCode")->willReturn(200);
        $response->method("getBody")->willReturn($body);
        $response->method("getHeaders")->willReturn($headers);
        return $response;
    }
}

class TestHandler
{
    private $tester;
    private $response;

    public function __construct($tester, $response)
    {
        $this->tester = $tester;
        $this->response = $response;
    }

    public function method($name, $id)
    {
        $this->tester->assertEquals("post", $name);
        $this->tester->assertEquals("5", $id);
        return $this->response;
    }
}