<?php
namespace HttpStack\App;

use DBTable;
use Dev\Template\Template;
use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\IO\FileLoader;
use HttpStack\Routing\Route;
use HttpStack\Routing\Router;
use HttpStack\DataBase\DBConnect;
use HttpStack\Container\Container;
use HttpStack\DocEngine\DocEngine;
use HttpStack\App\Models\PageModel;
use HttpStack\App\Models\TemplateModel;
use HttpStack\Datasource\FileDatasource;
use HttpStack\App\Datasources\DB\ActiveTable;
use HttpStack\App\Datasources\FS\JsonDirectory;

class App{
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
        $this->reportErrors();
        $GLOBALS["app"] = $this;
    }
    public function get(Route $route){
        $this->router->after($route);
    }
    public function loadRoutes(){
        $routesDir = $this->settings['appPaths']['routesDir'];
        $configs = [];
        //LOOP OVER THE ROUTES DIRECTORY
        //AND GET ROUTE ARRAYS FROM THE FILES
        foreach (glob($routesDir . '/*.php') as $file){
            //$file);
            $routes = include($file);
            //dd($routes);
            //LOOP OVER THE ROUTE ARRAYS AND REGISTER THWE ROUTES / MIDDLEWARES
            foreach($routes as $route){
                switch($route->getType()){
                    case "after":
                        $this->router->after($route);
                    break;

                    case "before":
                        $this->router->before($route);
                    break;
                }
            }
        }
        
    }
    public function getSettings(){
        return $this->settings;
    }
    public function getContainer(){
        return $this->container;
    }
    public function reportErrors(){
        if($this->debug){
            ini_set("display_errors", 1);
            ini_set("display_startup_errors", 1);
            error_reporting(32767);// E_ALL
        }
    }
    public function init(){
        $this->container->singleton(Container::class, $this->container);
        $this->container->singleton("app", $this);
        $this->container->singleton(self::class, $this);
        $this->container->singleton("dbConnect", function(){
            return new DBConnect();
        });
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
        $this->container->singleton("config", function()  {
            $configDir = APP_ROOT . "/config";
            $configs = [];
            foreach (glob($configDir . '/*.php') as $file) {
                $key = basename($file, '.php');
                $configs[$key] = include $file;
            }
            return $configs;
        });
        $this->container->singleton("template", function(){

            $objTemplate = new Template();
            $objTemplate->load("/var/www/html/HttpStack/App/Views/templates/base.html");

            return $objTemplate;

        });

        $this->container->singleton("template.model", function(){
            $dataDirectory = appPath("dataDir") . "/template";
            $dataSource = new JsonDirectory($dataDirectory, true);
            $tm = new TemplateModel($dataSource, "base", ["baseLayout" => config("template")["baseLayout"]]);
            
            return $tm;
        });
        $this->container->singleton("view.model", function($page){
            $dbConnect = $this->container->make("dbConnect");
            $dbDatasource = new ActiveTable($dbConnect, "pages", false);
            $viewModel = new PageModel($dbDatasource);

            return $viewModel;

        });
    }
    public function run(){
        $this->router->dispatch($this->request, $this->response, $this->container);
    }
}
?>