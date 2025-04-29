<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);
define("APP_INIT", true);
require '../vendor/autoload.php'; // If using Composer/autoload
/*
use App\DOM\DomTemplate;

// Load the base HTML template
$template = new DomTemplate('/var/www/html/App/Views/Templates/template.master.html', true);




// Add a custom function (optional)
$template->addFunction("doThis", function($arg) {
    return "Hello, " . htmlspecialchars($arg);
});

// Load or create the view content

$view = $template->loadView('/var/www/html/App/Views/Templates/testview.html');

// Set the assets path (optional)
$template->setAssetsPath('/var/www/html/public/assets/');

// Find the target node using DomHelper
$targetNode = $template->getElementById("data-view");
$sourceNode = $view->getElementById("data-view");
var_dump($sourceNode);  
// Insert the view into the target node

    $template->insertView($sourceNode, $targetNode);


// Set dynamic data for the template
$template->setData([
    'title' => 'My Dynamic Page',
    'username' => 'Juniper',
    'isLoggedIn' => true,
    'items' => ['Apples', 'Bananas', 'Cherries'],
    'viewKey' => 'testview'
]);

// Render and echo the final HTML
echo $template->render();
*/
use App\Router;
use App\Http\Request;
use App\Http\Response;
use App\Container;
use App\Controllers\Routes\{
    UserController,
    HomeController,
    ResumeController
};
use App\Controllers\Middleware\{
    TemplateMiddleware
};

use App\Traits\DomRoutines;

$objReq = new Request();
$objRes = new Response();
$objRouter = new Router();
$objContainer = new Container("/var/www/html/App/.config/config.app.php");

$objRouter->get('/home',  ['App\Controllers\Routes\HomeController', 'index']);
$objRouter->before('.*', ['App\Controllers\Middleware\TemplateMiddleware', 'init']);
$objRouter->dispatch($objReq, $objRes, $objContainer);
