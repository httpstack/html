<?php 
namespace HttpStack\Container;
use Closure;
use ReflectionClass;
use ReflectionParameter;
use ReflectionException;
use HttpStack\Exceptions\AppException;
use HttpStack\Contracts\ContainerInterface;

class Container implements ContainerInterface {
    protected $bindings = [];
    protected $instances = [];
    private array $props = [];


    public function __construct() {

    }


    public function bind(string $abstract, $concrete): void {
        $this->bindings[$abstract] = $concrete;
    }

    public function addProperty(string $name, $value) {
        $this->props[$name] = $value;
    }

    public function removeProperty(string $name) {
        unset($this->props[$name]);
    }

    public function getProperty(string $name) {
        return $this->props[$name] ?? null;
    }

    public function hasProperty(string $name) {
        return isset($this->props[$name]);
    }

    public function singleton(string $abstract, $concrete) {
        $this->bindings[$abstract] = $concrete;
        $this->instances[$abstract] = null; // Mark it as a singleton
    }

    public function make(string $abstract, mixed ...$params): mixed {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        return $this->resolve($abstract, $params);
    }

    protected function resolveDependencies(array $dependencies, array $parameters): array
    {
        $results = [];
        
        foreach ($dependencies as $dependency) {
            // If the parameter is in the given parameters, use it
            if (array_key_exists($dependency->name, $parameters)) {
                $results[] = $parameters[$dependency->name];
                continue;
            }
            
            // If the parameter is a class, resolve it from the container
            $results[] = $this->resolveClass($dependency);
        }
        
        return $results;
    }
    protected function resolveClass(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();
        
        // If the parameter doesn't have a type hint or is a built-in type, 
        // and is optional, use the default value
        if (!$type || $type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            
            throw new AppException(
                "Unresolvable dependency: $parameter in class {$parameter->getDeclaringClass()->getName()}"
            );
        }
        
        try {
            // Use the ReflectionNamedType API which works in PHP 7.4+
            $typeName = $type instanceof \ReflectionNamedType ? $type->getName() : (string)$type;
            return $this->make($typeName);
        } catch (AppException $e) {
            // If we can't resolve the class but the parameter is optional, 
            // use the default value
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            
            throw $e;
        }
    }
    public function call($callback, array $parameters = []): mixed
    {
        if (is_array($callback)) {
            // If the first element is a string (class name), instantiate it
            if (is_string($callback[0])) {
                $callback[0] = $this->make($callback[0]);
            }
            
            $reflectionMethod = new \ReflectionMethod($callback[0], $callback[1]);
            $dependencies = $reflectionMethod->getParameters();
            
            $parameters = $this->resolveDependencies($dependencies, $parameters);
            
            return $reflectionMethod->invokeArgs($callback[0], $parameters);
        }
        
        if ($callback instanceof Closure) {
            $reflectionFunction = new \ReflectionFunction($callback);
            $dependencies = $reflectionFunction->getParameters();
            
            $parameters = $this->resolveDependencies($dependencies, $parameters);
            
            return $reflectionFunction->invokeArgs($parameters);
        }
        
        if (is_string($callback) && strpos($callback, '::') !== false) {
            list($class, $method) = explode('::', $callback);
            return $this->call([$class, $method], $parameters);
        }
        
        if (is_string($callback) && method_exists($callback, '__invoke')) {
            $instance = $this->make($callback);
            return $this->call([$instance, '__invoke'], $parameters);
        }
        
        throw new AppException("Invalid callback provided to container->call()");
    }
    public function resolve(string $abstract, array $params = []) {
        if (!isset($this->bindings[$abstract])) {
            throw new \Exception("No binding found for {$abstract}");
        }

        $concrete = $this->bindings[$abstract];

        if (is_callable($concrete)) {
            return $concrete($this, ...$params);
        }

        if (is_string($concrete)) {
            return $this->build($concrete, $params);
        }

        return $concrete;
    }

    public function build(string $concrete, array $params = []) {
        $reflector = new \ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new \Exception("Cannot instantiate {$concrete}");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();
        $resolvedParams = [];

        foreach ($dependencies as $dependency) {
            $resolvedParams[] = $this->resolve($dependency->getType()->getName());
        }

        return $reflector->newInstanceArgs(array_merge($resolvedParams, $params));
    }

    public function makeCallable($handler): callable {
        // If the handler is already callable, return it as-is
        if (is_callable($handler)) {
            return $handler;
        }

        // If the handler is an array (e.g., [ClassName, MethodName])
        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;

            // Resolve the class using the container
            $instance = $this->make($class);

            // Ensure the method exists
            if (!method_exists($instance, $method)) {
                throw new \Exception("Method '{$method}' does not exist in class '{$class}'");
            }

            // Return a callable
            return function (...$params) use ($instance, $method) {
                return $instance->$method(...$params);
            };
        }

        // If the handler is a string, check if it's a global function
        if (is_string($handler) && function_exists($handler)) {
            return $handler;
        }

        throw new \Exception('Invalid handler provided');
    }
}
?>