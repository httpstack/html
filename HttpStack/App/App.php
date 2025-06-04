<?php
namespace HttpStack\App;
use HttpStack\Template\Template;
use HttpStack\App\Models\TemplateModel;
use HttpStack\Container\Container;
use HttpStack\DataBase\DBConnect;
use HttpStack\DocEngine\DocEngine;
use HttpStack\Routing\Router;
use HttpStack\Http\{Request,Response};
use HttpStack\IO\FileLoader;
use HttpStack\IO\Sources\FileDatasource;
use HttpStack\Routing\Route;
class App{
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
            error_reporting(E_ALL);
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
            $template = new Template();
            $template->readFile("base", "base");
            //normalize the document so it has doctype html head title body tags proper nested
            // get a data model for the template , model will be ro with 3 array
            //the template will request the model->base from data model and load it's data array with it
            //template mostly built we just got a
            // set title
            // set meta
            // get the resource array from model and 
            // load the resources from file array to build links and scripts and app and append them
            // get the other links array from model
            // use some Dom helpers to turn the links arrays into their respective navbars main,social,footer
            // add keys with value for navMain navSocial and navFooter to the templates datarray
            // replace the data with certain criteria on my expressions or data- attrbutes
            return $template;
        });
    
        $this->container->singleton("template.model", function(){
            $dataDirectory = appPath("dataDir") . "/template";
            $dataSource = new FileDatasource($dataDirectory);
            $tm = new TemplateModel($dataSource);
            return $tm;
        });
    }
    public function run(){
        $this->router->dispatch($this->request, $this->response, $this->container);
    }
}
?>