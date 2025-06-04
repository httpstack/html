<?php
namespace HttpStack\App\Controllers\Middleware;

use HttpStack\DocEngine\DocEngine;

class TemplateInit{
    protected DocEngine $template;
    public function __construct(){
        $container = box();
        //dd($container);
        $this->template = $container->make("template");

        //dd($model->getModel());
        //$this->model = $container->make();
        //dd($this->template);
    }
    public function process($req,$res,$container){
        $model = $container->make("template.model");
        /*
        $base['appName'] = "HTtpstACK";
        $model->set("base", $base);
        $model->save();
       */
        dd($model);
        $res->setHeader("Content-Type", "text/html");
        $res->setHeader("Middleware", "Template Loaded");
        
        $res->setBody($this->template->docToHTML());
    }
}
?>