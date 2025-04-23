<?php 
namespace App;
use App\Contracts\ContainerInterface;

class Container implements ContainerInterface {
    protected $bindings = [];
    protected $instances = [];
    private array $props = [];
    private Config $config;
    public array $settings = [];

    public function __construct(string $file = '') {
        if($file){
            $this->config = new Config();
            $this->config->load($file);
            $this->settings = $this->config->all();
        }
    }

    public function getSettings(): array {
        return $this->settings;
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

    public function make(string $abstract, array $params = []){
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        return $this->resolve($abstract, $params);
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