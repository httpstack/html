<?php
$settings = [
    "appName" => "HTTpstACK",
    "appVersion" => "",
    "appAuthor" => "",
    "appContact" => "",
    "docRoot" => DOC_ROOT,
    "appRoot" => APP_ROOT,
    "uriRoot" => "/",
    "appPaths" => [
        "configDir" => APP_ROOT . "/config",
        "dataDir" => APP_ROOT . "/data",
        "viewsDir" => APP_ROOT . "/Views/routes",
        "templatesDir" => APP_ROOT . "/Views/templates",
        "routeViewsDir" => APP_ROOT . "/Views/routes",
        "vendorAssetsDir" => DOC_ROOT . "/public/assets/enabled/vendor",
        "assetsDir" => DOC_ROOT . "/public/assets/enabled",
        "routesDir" => APP_ROOT . "/routes"
    ],
    "routeDefs" => "/routes/public.php",
    "baseLayout" => APP_ROOT . "/Views/templates/base.html",
];
return $settings;
