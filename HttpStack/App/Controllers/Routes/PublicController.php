<?php
namespace HttpStack\app\Controllers\Routes;

class PublicController{

    public function index($req,$res,$container,$matches){
        $res->setContentType("text/html")->setBody("Home Page");
        if(!$res->sent){
            $res->send();
        }
    }
    public function about($req, $res,$container,$matches){
        $res->setContentType("text/html")->setBody("About Page");
        if(!$res->sent){
            $res->send();
        }
    }

    public function contact($req, $res,$container,$matches){
        $res->setContentType("text/html")->setBody("Contact Page");
        if(!$res->sent){
            $res->send();
        }
    }

    public function resume($req, $res,$container,$matches){
        $res->setContentType("text/html")->setBody("Resume Page");
        if(!$res->sent){
            $res->send();
        }
    }
}
?>