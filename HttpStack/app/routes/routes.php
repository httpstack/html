<?php
use HttpStack\app\Controllers\Routes\PublicController;
$routes = [
    "/about" => [
                    [new PublicController,"about"], 
                    "GET", 
                    "route"
                ],
];
return $routes;
?>