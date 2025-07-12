<?php
namespace HttpStack\App\Controllers\Routes;
use HttpStack\Model\AbstractModel;
class PublicController{

    public function index($req,$res,$container,$matches){
        $res->setContentType("text/html");


        $template = $container->make("template");
        $template->setVariables([
            "sessUser"=>"Chris",
            "viewContent"=>"CTA or other content"
        ]);
        $template->importView("/var/www/html/HttpStack/App/Views/templates/testView.html");
        //var_dump($template);
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