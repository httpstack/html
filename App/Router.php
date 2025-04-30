<?php
namespace App;

class Router {
    private array $routes = [];
    private array $routeMiddleWare = [];
    private $container;

    public function __construct($container = null) {
        $this->container = $container;
    }

    public function addRoute(string $method, string $path, $handler): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $this->parsePath($path),
            'handler' => $handler,
        ];
    }
    public function before(string $path, $handler): void {
        $this->routeMiddleWare[] = [
            'path' => $this->parsePath($path),
            'handler' => $handler,
        ];
    }
    public function get(string $path, $handler): void {
        $this->addRoute('GET', $path, $handler);
    }

    private function parsePath(string $path): string {
        // Convert placeholders like /user/{id} to regex /user/([^/]+)
        return preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
    }
    
    /**
     * Converts a handler into a callable.
     *
     * @param mixed $handler The handler to convert.
     * @return callable The converted callable.
     * @throws \Exception If the handler is invalid.
     */

    protected function makeCallable($handler, $arrParams = []): callable
    {
        // If the handler is an array (e.g., [ClassName, MethodName])
        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
    
            // Check if the method exists
            if (method_exists($class, $method)) {
                $reflection = new \ReflectionMethod($class, $method);
                if (!$reflection->isStatic()) {
                    // Check if the class exists
                    $class = new $class();
                    // Create an instance of the class if the method is not static
                }
            }
    
            return [$class, $method];
        }
    
        // If the handler is a callable (e.g., a closure or function), return it as-is
        if (is_callable($handler)) {
            return $handler;
        }
    
        // If the handler is a class name, instantiate it and call __invoke
        if (is_string($handler) && class_exists($handler)) {
            // Check if the class has a __invoke method
            $instance = new $handler();
    
            if (method_exists($instance, '__invoke')) {
                return $instance; // The __invoke method will be called
            }
    
            throw new \Exception("Class '{$handler}' does not have an __invoke method.");
        }
    
        throw new \Exception('Invalid route handler');
    }
    private function processQueryString($uri, $request): string {
        // Separate the path and query string
        $uriParts = explode('?', $uri, 2);
        $path = $uriParts[0]; // The path part of the URI
        $queryString = $uriParts[1] ?? ''; // The query string part, if present
    
        // Parse query string into an associative array
        parse_str($queryString, $queryParams);
    
        // Set query string parameters on the Request object
        $request->setQueryParams($queryParams);
    
        return $path; // Return the path part of the URI
    }
    public function dispatch($request, $response, $container = null): void {
        // Get the method and URI from the Request object

        //
        $method = $request->getMethod();
        $uri = $request->getUri();
    
        $path = $this->processQueryString($uri, $request);


        $middlewareIndex = 0;
        $middlewareCount = count($this->routeMiddleWare);
        
        $next = function () use (&$middlewareIndex, $middlewareCount, $request, $response, $container, &$next) {
            if ($middlewareIndex < $middlewareCount) {
                $mw = $this->routeMiddleWare[$middlewareIndex++];
                if ($mw['path'] === '.*' || preg_match('#^' . $mw['path'] . '$#', $request->getUri())) {
                    $callable = $this->makeCallable($mw['handler'], [$request, $response, $container, $next]);
                    $callable($request, $response, $container, $next);
                } else {
                    $next();
                }
            }
        };
        
        // Start the middleware chain
        $next();
        // Process middleware
        foreach ($this->routeMiddleWare as $middleware) {
            if ($middleware['path'] === '.*' || preg_match('#^' . $middleware['path'] . '$#', $path)) {
                // Resolve the middleware handler into a callable
                $callable = $this->makeCallable($middleware['handler']);
    
                // Call the middleware with the required arguments
                $callable($request, $response, $container, $next);
            }
        }
    
        // Match the route
        foreach ($this->routes as $route) {
            if ($method === $route['method'] && preg_match('#^' . $route['path'] . '$#', $path, $matches)) {
                array_shift($matches); // Remove the full match
    
                // Resolve the handler into a callable
                $callable = $this->makeCallable($route['handler']);
    
                // Call the handler with the required arguments
                $callable($request, $response, $container, ...$matches);
                return;
            }
        }
    
        // If no route matches, return a 404 response
        $response->setStatusCode(404);
        //$response->setBody('<h1>404 Not Found</h1>');
        $response->send();
    }
}
?>