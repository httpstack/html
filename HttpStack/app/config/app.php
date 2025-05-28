<?php
$settings = [
    "appName" => "",
    "appVersion" => "",
    "appAuthor" => "",
    "appContact" => "",
    "docRoot" => DOC_ROOT,
    "appRoot" => APP_ROOT,
    "uriRoot" => "/",
    "appPaths" => [
        "configDir" => "/config",
        "dataDir" => "/data",
        "viewsDir" => "/Views/routes",
        "templatesDir" => "/Views/templates",
        "assetsDir" => "/assets",
        "routesDir" => "/routes"
    ],
    "routeDefs" => "/routes/routes.php",
];
return $settings;
?>