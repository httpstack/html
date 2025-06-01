<?php
namespace HttpStack\Routing;
class Router {
    private $routes = [];
    private $beforeMiddleware = [];

    public function addRoute($method, $path, $handler){
        $this->routes[$method][$path] = $handler;
    }
    public function addMiddleware($method, $path, $handler){
        $this->beforeMiddleware[$method][$path] = $handler;
    }
    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }
    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }
    public function beforeGet($pattern, $middleware) {
        $this->beforeMiddleware['GET'][$pattern] = $middleware;
    }   
    public function dispatch($request, $response, $container) { 
        $method = $request->getMethod();
        $uri = $request->getUri();
        //dd($this->beforeMiddleware);
        /**
         * LOOP MIDDLEWARES
         */
        foreach($this->beforeMiddleware[$method] as $pattern => $middleware){
            // Convert {param} to regex
            $regex = preg_replace('/\{\w+\}/', '([^/]+)', $pattern);
            // Convert wildcard * to regex
            $regex = str_replace('*', '.*', $regex);
            // Allow full match for .*
            if ($regex === '.*') {
                $regex = '.*';
            }
            $matches = [];
            if (preg_match("#^$regex$#", $uri)) {
                if(is_array($middleware)){
                    list($className, $methodName) = $middleware;
                    $instance = new $className();
                    $callable = [$instance,$methodName];
                }else{
                    $callable = $middleware;
                }
               // $container->call($mw, [$request, $response, $container, $matches]);
                call_user_func_array($callable, [$request,$response,$container,$matches]);
            }
        }
        /**
         * LOOP WARES
         * 
         */
        foreach($this->routes[$method] as $pattern => $ware){
            $matches = [];
            $regex = preg_replace('/\{\w+\}/', '([^/\/]+)', $pattern);
            
            if(preg_match("#$regex#", $request->getUri(),$matches)){
                //$container->call($this->routes[$method][$key], [$request, $response, $container, $matches]);
                if(is_array($ware)){
                    list($className, $methodName) = $ware;
                    $instance = new $className();
                    $callable = [$instance,$methodName];
                }else{
                    $callable = $ware;
                }
                call_user_func_array($callable, [$request,$response,$container,$matches]);
            }
        }
    }
}

?>