<?php

namespace App\Controllers\Routes;

class HomeController {
    public function index($request, $response, $container) {
       
        $strTemplate = $response->getBody();
        
        $strTemplate = str_replace('{{title}}', 'Home', $strTemplate);
        $strTemplate = str_replace('{{content}}', "Homie", $strTemplate);
        $response->setBody($strTemplate);
        $response->send();
    }
}