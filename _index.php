<?php
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
$app->get("/home", function($req,$res){
    $res->setContentType("text/html")->setBody("Home Page");
    $res->send();
});

$app->run();
?>