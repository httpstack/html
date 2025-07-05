<?php
namespace HttpStack\App;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\IO\FileLoader;
use HttpStack\Routing\Route;
use HttpStack\Routing\Router;
use HttpStack\Template\Template;
use HttpStack\DataBase\DBConnect;
use HttpStack\Container\Container;
use HttpStack\DocEngine\DocEngine;
use HttpStack\App\Models\TemplateModel;
use HttpStack\Datasource\FileDatasource;
use HttpStack\App\Datasources\FS\JsonDirectory;
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
            $template = new Template();
            
            /**
             * This takes the namespace that you will use to reffer to the file
             * and the base file name that will be used to load the file
             * 
             * loadFile(nameSpace, baseFileName);
             */
            $html = $template->loadFile("base", "base");
            //
            //normalize the document so it has doctype html head title body tags proper nested
            $html = $template->normalizeHtml($html);
            //Update the cached version to the normalized one
            $template->setFile("base", $html);
            //set the cached version to the normalized one
            $template->setTemplate("base");
           
            /**
             * $model is getting a datamodel that is sourced by a directory of json files
             * the model will be used to get the base data for the template
             * the model will be used to get the resources array for the template
             * the model will be used to get the links array for the template
             * 
             * DataModels have a simple $model array with key => value
             * and are a reflection of an ACTUAL datasource.
             * The getter and setter or get() and set() , dont wanna confuse
             * these methods work on the virtual representation of the data
             * and not the actual data source. You can set your DS (datasource)
             * to be read/write or read only to prevent accidents.
             * when you fetch() or save() these methods will dispatch the 
             * datasource and refresh the model with a current snapshot of that source 
             * or save the model to the source.
             */
            $model = $this->container->make("template.model");
            $template->setVar($model->getAll());
            $appName = $model->get("base")['appName'] ?? "A Default App Name";
            $assets = $model->get("assets") ?? [];
            $links = $model->get("links") ?? [];
            $template->loadAssets($assets);
            $template->bindAssets($template->getAssets());
            var_dump($appName);
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
        $this->container->singleton("docEngine", function(){
            return new DocEngine();
        });
        $this->container->singleton("dbModel", function(){
            $dbConnect = $this->container->make("dbConnect");
            $dbDS = new DBDatasource($dbConnect, "assets");
            return $dbModel;
        });
        $this->container->singleton("template.model", function(){

            $dataDirectory = appPath("dataDir") . "/template";
            $dataSource = new JsonDirectory($dataDirectory, true);
            $tm = new TemplateModel($dataSource);
            
            return $tm;
        });
    }
    public function run(){
        $this->router->dispatch($this->request, $this->response, $this->container);
    }
}
?>