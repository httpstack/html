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
use App\Controllers\Middleware\TemplateMiddleware;

$objReq = new Request();
$objRes = new Response();
$objRouter = new Router();
$objContainer = new Container("/var/www/html/App/.config/config.app.php");
$objRouter->get('/home',  ['App\Controllers\Routes\HomeController', 'index']);
$objRouter->before('.*', ['App\Controllers\Middleware\TemplateMiddleware', 'init']);
//$objRouter->get('/user/{id}', ['App\Controllers\Routes\UserController', 'show']);
$objRouter->dispatch($objReq, $objRes, $objContainer);