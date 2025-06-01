<?php
require_once(__DIR__ . "/../HttpStack/App/init.php");
require_once(DOC_ROOT . "/HttpStack/App/util/helpers.php");

use HttpStack\App\App;

$app = new App();

$app->loadRoutes();
//[function(){return new class}]



$app->run();
?>