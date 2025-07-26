<?php
use HttpStack\App\Controllers\Routes\PublicController;
use HttpStack\Routing\Route;

$home = new Route("GET","/home", box()->makeCallable(["HttpStack\App\Controllers\Routes\PublicController", "index"]),"after");
$resume = new Route("GET","/resume", box()->makeCallable(["HttpStack\App\Controllers\Routes\PublicController", "resume"]),"after");
$contact = new Route("GET", "/contact", box()->makeCallable(['HttpStack\App\Controllers\Routes\PublicController', "contact"]), "after");
$about = new Route("GET", "/about", box()->makeCallable(["HttpStack\App\Controllers\Routes\PublicController", "about"]), "after");
return [$home,$resume,$contact,$about];
?>