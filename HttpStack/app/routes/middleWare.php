<?php
use HttpStack\app\Controllers\Middleware\SessionController;
$container = app()->getContainer();

$routes = [
    ".*" => [
                ['HttpStack\app\Controllers\Middleware\SessionController',"process"], 
                "GET", 
                "mw"
            ],
];
return $routes;
?>