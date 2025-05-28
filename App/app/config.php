<?php
define("DOC_ROOT", "/var/www/html");
$settings = [
    "appName" => "",
    "appVersion" => "",
    "appAuthor" => "",
    "appContact" => "",
    "docRoot" => DOC_ROOT,
    "uriRoot" => "/",
    "appPaths" => [
        "configDir" => "/config",
        "dataDir" => "/data",
        "viewsDir" => "/Views/routes",
        "routesPath" => "/routes/routes.php"
    ]
];
return $settings;
?>