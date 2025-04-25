<?php

namespace App\Controllers\Routes;

class HomeController {
    public function index($request, $response, $container) {
        $strTemplate = "{{title}} {{content}}";
        $strTemplate = str_replace('{{title}}', 'Home', $strTemplate);
        $strTemplate = str_replace('{{content}}', "base url is: ", $strTemplate);
        $response->setBody($strTemplate);
        $response->send();
    }
}