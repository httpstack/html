<?php
namespace HttpStack\App;
use HttpStack\Container\Container;
use HttpStack\Routing\Router;
use HttpStack\Http\{Request,Response};
use HttpStack\IO\FileLoader;
class Application{
    protected Container $container;
    protected Request $request;
    protected Response $response;
    protected Router $router;
    protected array $settings = [];
    protected FileLoader $fileLoader;
    public bool $debug = true;
    public function __construct(string $appPath = "/var/www/html/App/app") {
        $this->router = new Router();
        $this->request = new Request();
        $this->response = new Response();
        $this->container = new Container();
        
        //INIT WILL BIND ALL THE INSTANCES TO THE CONTAINER
        $this->init();
        //GET SETTINGS FOR APP
        $this->settings = $this->container->make("config")['app'];
        $GLOBALS["app"] = $this;
    }
    public function loadRoutes(){
        $routeDefs = $this->settings['appRoot'] . $this->settings['routeDefs'];
        
        $routes = include($routeDefs);
        dd($routes);
        foreach($routes as $uri => list($handler, $method)){
            $this->router->addRoute($method, $uri, $handler);
        }
    }
    public function getContainer(){
        return $this->container;
    }
    public function get(string $uri, callable $handler){
        $this->router->get($uri, $handler);
    }
    public function setDebug(){
        if($this->debug){
            ini_set("display_errors", 1);
            ini_set("display_startup_errors", 1);
            error_reporting(E_ALL);
        }
    }
    public function init(){
        $this->container->singleton(Container::class, $this->container);
        $this->container->singleton("app", $this);
        $this->container->singleton(self::class, $this);
        $this->container->singleton("router", $this->router);
        $this->container->singleton("request", $this->request);
        $this->container->singleton("response", $this->response);
        
        $this->container->singleton("fileLoader", function() {
            $fl = new FileLoader();
            foreach($this->settings['appPaths'] as $name => $path){
                $fl->mapDirectory($name, $path);
            }
            return $fl;
        });
        $this->container->singleton("config", function() {
            $configDir = APP_ROOT . "/config";
            $configs = [];
            foreach (glob($configDir . '/*.php') as $file) {
                $key = basename($file, '.php');
                $configs[$key] = include $file;
            }
            return $configs;
        });
    }
    public function run(){
        $this->router->dispatch($this->request, $this->response);
    }
}
?>