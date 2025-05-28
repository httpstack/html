<?php
define("DOC_ROOT", "/var/www/html");
spl_autoload_register(function($className){
    
    $file = __DIR__ . "/" . str_replace('\\', '/', $className) . '.php';
    //print_r("Loading class: $className from $file\n");
    if (file_exists($file)) {
        
        require_once $file;
    }
});
use App\Application;

$app = new Application();

$app->loadRoutes();
$cnt = $app->getContainer();
$fl = $cnt->make("fileLoader");
$fl->mapDirectory("appRoot", DOC_ROOT . "/App/app");
echo $fl->findFile("routes", "appRoot", "php");
//var_dump($fl);
$app->get("/home", function($req,$res){
    $res->setContentType("text/html")->setBody("Home Page");
    $res->send();
});

$app->run();
?>