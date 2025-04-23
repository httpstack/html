<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Router;
use App\Http\Request;
use App\Http\Response;
use App\Container;
use App\Controllers\Routes\UserController;

$objReq = new Request();
$objRes = new Response();
$objRouter = new Router();
$objContainer = new Container("/var/www/html/App/.config/config.app.php");
$objContainer->addProperty('global', 'test');
$objRouter->before('.*', function ($request, $response, $container) {
    $response->setHeader('X-Middleware', 'Executed');
});

//$objRouter->before('.*', ['MiddlewareClass', 'handle']);
//$objRouter->before('.*', 'globalMiddlewareFunction');
//$objRouter->before('.*', 'MiddlewareClass');
$objRouter->before('.*', function ($objReq, $objRes, $objContainer)  {
    //by usin the container use($objCOntainer) we can bind a tool that ALL routes can see
    // we are "usin" it from the outside scope (parent)
    $objContainer->bind('template', function(){
        return file_get_contents('/var/www/html/App/Views/Templates/template.base.html');
    });
    $objRes->setHeader('Content-Type', 'text/html');
});
$objRouter->get('/user/{id}', ['App\Controllers\Routes\UserController', 'show']);
//$objRouter->get('/home', [new HomeController()]);
//$objRouter->get('/about', 'AboutController');
$objRouter->get('/home', function($objReq, $objRes,  $objContainer)  {
    $strTemplate = $objContainer->make('template');
    $test = $objContainer->getSettings()['paths']['base'];
    $strTemplate = str_replace('{{title}}', 'Home', $strTemplate);
    $strTemplate = str_replace('{{content}}', "base url is: $test", $strTemplate);
    $objRes->setBody($strTemplate);
    $objRes->send();
});

//now the container will enter the object and be passed to route with route specific saervices
$objRouter->dispatch($objReq, $objRes, $objContainer);
?>