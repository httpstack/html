<?php
namespace App\Controllers\Routes;

class UserController {
    public function show($request, $response, $container, $id) {
        $strTemplate = $container->make('template');
        $strTemplate = str_replace('{{title}}', 'Home', $strTemplate);
        $strTemplate = str_replace('{{content}}', 'Welcome to the User id#'.$id.', page!', $strTemplate);
        $response->setBody($strTemplate);
        $response->send();
    }
}
?>