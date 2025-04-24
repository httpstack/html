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
$objRouter->before('.*', 'TemplateMiddleware');
$objRouter->before('^/user/.*$', function ($request, $response, $container, $next) {
    // Check if the user is authenticated
    if (!true) {
        $response->setStatusCode(401);
        $response->setBody('<h1>Unauthorized</h1>');
        $response->send();
        return; // Stop the chain
    }

    // Pass control to the next middleware
    $next();
});

$objRouter->get('/user/{id}', ['App\Controllers\Routes\UserController', 'show']);
//$objRouter->get('/home', [new HomeController()]);
//$objRouter->get('/about', 'AboutController');
/*
 * 
 * 

$objRouter->get('/home', function($objReq, $objRes,  $objContainer)  {
    $strTemplate = $objRes->getBody();
    $strTemplate = str_replace('{{title}}', 'Home', $strTemplate);
    $strTemplate = str_replace('{{content}}', "base url is: ", $strTemplate);
    $objRes->setBody($strTemplate);
    $objRes->send();
});
 */
$objRouter->get('/debug', function ($request, $response, $container) {
    // Start output buffering
    ob_start();

    // Perform var_dump
    var_dump($container);

    // Capture the output
    $output = ob_get_clean();

    // Set the output as the response body
    $response->setBody('<pre>' . htmlspecialchars($output) . '</pre>');
    $response->send();
});
//now the container will enter the object and be passed to route with route specific saervices
$objRouter->dispatch($objReq, $objRes, $objContainer);
?>