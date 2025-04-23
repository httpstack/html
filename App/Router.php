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
    protected function makeCallable($handler): callable
    {
        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;

            // Check if the method is static
            if (method_exists($class, $method)) {
                $reflection = new \ReflectionMethod($class, $method);
                if (!$reflection->isStatic()) {
                    // Create an instance of the class if the method is not static
                    $class = new $class();
                }
            }

            return [$class, $method];
        }

        if (is_callable($handler)) {
            return $handler;
        }

        throw new \Exception('Invalid route handler');
    }
    public function dispatch($request, $response, $container = null): void {
        // Get the method and URI from the Request object
        $method = $request->getMethod();
        $uri = $request->getUri();
    
        // Separate the path and query string
        $uriParts = explode('?', $uri, 2);
        $path = $uriParts[0]; // The path part of the URI
        $queryString = $uriParts[1] ?? ''; // The query string part, if present
    
        // Parse query string into an associative array
        parse_str($queryString, $queryParams);
    
        // Set query string parameters on the Request object
        $request->setQueryParams($queryParams);
    
        // Process middleware
        foreach ($this->routeMiddleWare as $middleware) {
            if ($middleware['path'] === '.*' || preg_match('#^' . $middleware['path'] . '$#', $path)) {
                // Resolve the middleware handler into a callable
                $callable = $this->makeCallable($middleware['handler']);
    
                // Call the middleware with the required arguments
                $callable($request, $response, $container);
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
        $response->setBody('<h1>404 Not Found</h1>');
        $response->send();
    }
}
?>