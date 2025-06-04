<?php
use HttpStack\App\Controllers\Routes\PublicController;
use HttpStack\Routing\Route;

$home = new Route("GET","/home", ['HttpStack\App\Controllers\Routes\PublicController', "index"],"after");
$resume = new Route("GET","/resume", [new PublicController(), "resume"],"after");
$contact = new Route("GET", "/contact", [new PublicController(), "contact"], "after");
$about = new Route("GET", "/about", [new PublicController(), "about"], "after");
return [$home,$resume,$contact,$about];
?>