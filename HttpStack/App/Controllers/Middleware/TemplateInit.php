<?php
namespace HttpStack\App\Controllers\Middleware;

use HttpStack\Template\Template;
use HttpStack\DocEngine\DocEngine;
class TemplateInit{
    protected Template $template;
    public function __construct(){
        $container = box();
        //dd($container);
        $this->template = $container->make("template");

        //dd($model->getModel());
        //$this->model = $container->make();
        //dd($this->template);
    }
    public function process($req,$res,$container){
        //$model = $container->make("template.model");
        //consoleLog($this->template->getCachedFile("base"));
        /*
        $base['appName'] = "HTtpstACK";
        $model->set("base", $base);
        $model->save();
       */
        //dd($model);
        $res->setHeader("Content-Type", "text/html");
        $res->setHeader("Middleware", "Template Loaded");
        
        $res->setBody("mw done");
    }
}
?>