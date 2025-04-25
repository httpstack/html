<?php

namespace App\Controllers\Routes;

class HomeController {
    public function index($request, $response, $container) {
        $template = $container->make('template');
        $strTemplate = $template->saveHTML();
        $strTemplate = str_replace('{{title}}', 'Home', $strTemplate);
        $strTemplate = str_replace('{{content}}', "Homie", $strTemplate);
        $response->setBody($strTemplate);
        $response->send();
    }
}