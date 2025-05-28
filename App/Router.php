<?php
namespace App;
class Router {
    private $routes = [];
    private $beforeMiddleware = [];

    public function addRoute($method, $path, $handler){
        $this->routes[$method][$path] = $handler;
    }
    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }
    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }
    public function before($pattern, $middleware) {
        $this->beforeMiddleware[$pattern] = $middleware;
    }
    public function dispatch($request, $response) { 
        $method = $request->getMethod();
        $uri = $request->getUri();
        foreach($this->beforeMiddleware as $pattern => $mw){
            // Convert {param} to regex
            $regex = preg_replace('/\{\w+\}/', '([^/]+)', $pattern);
            // Convert wildcard * to regex
            $regex = str_replace('*', '.*', $regex);
            // Allow full match for .*
            if ($regex === '.*') {
                $regex = '.*';
            }
            if (preg_match("#^$regex$#", $uri)) {
                call_user_func($mw, $request,$response);
            }
        }
        foreach($this->routes[$method] as $key => $value){
            $matches = [];
            $pattern = preg_replace('/\{\w+\}/', '([^/\/]+)', $key);
            
            if(preg_match("#$pattern#", $request->getUri(),$matches)){
                call_user_func($this->routes[$method][$key], $request,$response, $matches);
            }
        }
    }
}

?>