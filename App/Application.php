<?php
namespace App;
use App\Container;
use App\Router;
use App\Http\{Request,Response};
use App\FileLoader;
class Application{
    protected Container $container;
    protected Request $request;
    protected Response $response;
    protected Router $router;
    protected array $settings;
    protected FileLoader $fileLoader;
    protected bool $debug;
    public function __construct(string $appPath = "/var/www/html/App/app") {
        $this->router = new Router();
        $this->request = new Request();
        $this->response = new Response();
        $this->container = new Container();
        $this->fileLoader = new FileLoader();

        $this->fileLoader->mapDirectory("config",$appPath . "/config");
        $file = $this->fileLoader->findFile("config", "config", "php");
        
        $this->container->singleton(Container::class, $this->container);
        $this->container->singleton("app", $this);
        $this->container->singleton(self::class, $this);
        $this->container->singleton("router", $this->router);
        $this->container->singleton("request", $this->request);
        $this->container->singleton("response", $this->response);
    }
    public function loadRoutes(){
        $routes = include("/var/www/html/App/app/routes.php");
        foreach($routes as $uri => list($handler, $method)){
            $this->router->addRoute($method, $uri, $handler);
        }
    }
    public function get(string $uri, callable $handler){
        $this->router->get($uri, $handler);
    }
    public function init(){

    }
    public function run(){
        $this->router->dispatch($this->request, $this->response);
    }
}
?>