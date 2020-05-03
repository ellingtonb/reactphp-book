<?php

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\Response;

class Router
{
    private $routes = [];
    
    private $loop;
    
    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }
    
    public function __invoke(ServerRequestInterface $request)
    {
        $path = $request->getUri()->getPath();
        echo "Request for: $path\n";
        
        $handler = $this->routes[$path] ?? $this->notFound($path);
        return $handler($request, $this->loop);
    }
    
    private function notFound($path)
    {
        return function () use ($path) {
            return new Response(
                404,
                [ 'Content-Type' => 'text/plain' ],
                "No request handler found for $path"
            );
        };
    }
    
    public function add($path, callable $handler)
    {
        $this->routes[$path] = $handler;
    }
    
    public function load($filename)
    {
        $routes = require $filename;
        
        foreach ($routes as $path => $handler) {
            $this->add($path, $handler);
        }
    }
}
