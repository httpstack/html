<?php
namespace HttpStack\App\Controllers\Routes;
use HttpStack\Model\AbstractModel;
class PublicController{

    public function index($req,$res,$container,$matches){
        $res->setContentType("text/html");
        $template = $container->make("template");
        $template->set("pageView", "<div>im in a div </div>");
        $fl = $container->make("fileLoader");
        $testView = $fl->findFile("testView", null, "html");
        $template->addTemplate("view", $testView );
        $html = $template->render();
 
        
        $res->setBody($html);
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