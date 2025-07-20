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


        //////////////////////////////
        //this part of the code can be put into a view class
        $template = $container->make("template");
        //we will make the view instance passing a index to
        // get the views unique content 

        //variables to replace can bet set within the view as it is 
        //template related, therefore view related2
        $template->setVariables([
            "sessUser"=>$_SESSION['sessUser'],
            "viewContent"=>"View"
        ]);

        //the actual view thats loaded, containing expressions of its own
        $template->importView("/var/www/html/HttpStack/App/Views/templates/testView.html");
        //$vm = $container->make("view.model", "Home");
        //var_dump($template);
        $html = $template->render();
        ////////////////////////////

        
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