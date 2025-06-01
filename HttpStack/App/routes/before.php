<?php
use HttpStack\Routing\Route;
use HttpStack\App\Controllers\Middleware\SessionController;
use HttpStack\App\Controllers\Middleware\TemplateInit;

$global = new Route("GET",".*",[new SessionController,'process'], "before");
$global->addHandler([new TemplateInit(), 'process']);
return [$global];
?>