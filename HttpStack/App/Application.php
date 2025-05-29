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
    public bool $debug = false;
    public function __construct(string $appPath = "/var/www/html/App/app") {
        $this->router = new Router();
        $this->request = new Request();
        $this->response = new Response();
        $this->container = new Container();
        
        //INIT WILL BIND ALL THE INSTANCES/SERVICES TO THE CONTAINER
        $this->init();

        //GET SETTINGS FOR APP
        $this->settings = $this->container->make("config")['app'];
        $GLOBALS["app"] = $this;
    }
    public function loadRoutes(){
        $routesDir = APP_ROOT . "/" . $this->settings['appPaths']['routesDir'];
        $configs = [];
        //LOOP OVER THE ROUTES DIRECTORY
        //AND GET ROUTE ARRAYS FROM THE FILES
        foreach (glob($routesDir . '/*.php') as $file){
            dd($file);
            $routes = include($file);
            dd($routes);
            //LOOP OVER THE ROUTE ARRAYS AND REGISTER THWE ROUTES / MIDDLEWARES
            foreach($routes as $uri => list($handler, $method, $type)){
                switch($type){
                    case "route":
                        $this->router->addRoute($method, $uri, $handler);
                    break;

                    case "mw":
                        $this->router->addMiddleware($method,$uri,$handler);
                    break;
                }
            }
        }
        
    }
    public function getContainer(){
        return $this->container;
    }
    public function get(string $uri, callable $handler){
        $this->router->get($uri, $handler);
    }
    public function beforeGet($uri,$handler){
        $this->router->beforeGet($uri, $handler);
    }
    public function reportErrors(){
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
        $this->router->dispatch($this->request, $this->response, $this->container);
    }
}
?>