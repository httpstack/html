<?php

use HttpStack\Routing\Route;
use HttpStack\App\Controllers\Routes\HomeController;
use HttpStack\App\Controllers\Routes\LoginController;
use HttpStack\App\Controllers\Routes\PublicController;
use HttpStack\App\Controllers\Routes\ResumeController;


$fncHome = box()->makeCallable(["HttpStack\App\Controllers\Routes\HomeController", "index"]);
$fncLogin = box()->makeCallable(["HttpStack\App\Controllers\Routes\LoginController", "login"]);
$fncResume = box()->makeCallable(["HttpStack\App\Controllers\Routes\ResumeController", "index"]);
$fncContact = box()->makeCallable(["HttpStack\App\Controllers\Routes\ContactController", "index"]);
$fncStack = box()->makeCallable(["HttpStack\App\Controllers\Routes\ServicesController", "index"]);
$login = new Route("GET", "/login", $fncLogin, "after");
$root = new Route("GET", "/", $fncHome, "after");
$home = new Route("GET", "/home", $fncHome, "after");
$resume = new Route("GET", "/resume", $fncResume, "after");
$contact = new Route("GET", "/stacks", $fncStack, "after");
$about = new Route("GET", "/contact", $fncContact, "after");
return [$login, $root, $home, $resume, $contact, $about];
