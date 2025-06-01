<?php
namespace HttpStack\app\Controllers\Routes;

class PublicController{

    public function index(){

    }
    public function about($req, $res){
        $res->setContentType("text/html")->setBody("About Page");
        $res->send();
    }
}
?>