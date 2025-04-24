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

//$objRouter->before('.*', 'globalMiddlewareFunction');
//$objRouter->before('.*', 'MiddlewareClass');
$objRouter->before('.*', function ($request, $response, $container, $next) {
    // Add a header to the response
    $response->setHeader('X-Middleware', 'Executed');

    // Pass control to the next middleware
    $next();
});
$objRouter->before('.*', function ($request, $response, $container, $next) {
    // Add a header to the response
    $response->setHeader('X-2ndMiddle', 'Executed');

    // Pass control to the next middleware
    $next();
});
$objRouter->before('^/user/.*$', function ($request, $response, $container, $next) {
    // Check if the user is authenticated
    if (!false) {
        $response->setStatusCode(401);
        $response->setBody('<h1>Unauthorized</h1>');
        $response->send();
        return; // Stop the chain
    }

    // Pass control to the next middleware
    $next();
});
$objRouter->dispatch($objReq, $objRes, $objContainer);