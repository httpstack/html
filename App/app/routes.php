<?php
use App\app\Controllers\Routes\PublicController;
$routes = [
    "/about" => [[new PublicController,"about"], "GET"],
];
return $routes;
?>