
<!-- RESTORE TO:::::: /var/www/html/public -->
<?php
require_once(__DIR__ . "/HttpStack/app/init.php");
require_once(__DIR__ . "/HttpStack/app/util/helpers.php");

use HttpStack\App\App;


$app = new App();

$app->loadRoutes();


$app->get("/home", function($req,$res){
    $res->setContentType("text/html")->setBody("Home Page");
    $res->send();
});

$app->run();
?>