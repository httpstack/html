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
        $base = $model->get("base");
        $base['appName'] = "yadda";
        $model->set("base", $base);
        $model->save();
       // dd($res);
       echo "processing";
        $res->setHeader("Content-Type", "text/html");
        $res->setHeader("Middleware", "Session Started");
        
        $res->setBody($this->template->docToHTML());
    }
}
?>