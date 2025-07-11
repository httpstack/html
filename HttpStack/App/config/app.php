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
        "componentsDir" => DOC_ROOT . "/public/assets/js/components",
        "dataDir" => APP_ROOT . "/data",
        "viewsDir" => APP_ROOT . "/Views/routes",
        "templatesDir" => APP_ROOT . "/Views/templates",
        "vendorAssetsDir" => DOC_ROOT . "/public/assets/vendor",
        "assetsDir" => DOC_ROOT . "/public/assets",
        "routesDir" => APP_ROOT . "/routes"
    ],
    "routeDefs" => "/routes/public.php",
    "template" =>[
        "baseLayout" => "base.html",
    ]
];
return $settings;
?>