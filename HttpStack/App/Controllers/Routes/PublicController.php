<?php
namespace HttpStack\App\Controllers\Routes;
use HttpStack\Model\AbstractModel;
class PublicController{
    public function __construct(){
        //i feel like here some initail view shit con be setup or pulled from the container
        //if not no biggie
    }
    public function index($req,$res,$container,$matches){
        $res->setContentType("text/html");



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