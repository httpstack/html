<?php

namespace App\Controllers\Routes;

class HomeController {
    public function index($request, $response, $container) {
       
        $template = $container->getProperty('template');
        $viewSlot = $container->getProperty('viewContainer');
        $view = $template->loadView("../App/Views/home.html");
        $sourceNode = $view->getElementById("data-view");
        //var_dump($viewSlot);
        //var_dump($sourceNode);
        $template->insertView($sourceNode, $viewSlot);
        $template->setData([
            'username' => 'Juniper',
            'isLoggedIn' => true,
            'items' => ['Apples', 'Bananas', 'Cherries'],
            'viewKey' => 'home'
        ]);
        
        
        $response->setBody($template->render());
        $response->send();
    }
}