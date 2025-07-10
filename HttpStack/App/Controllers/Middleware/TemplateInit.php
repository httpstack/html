<?php
namespace HttpStack\App\Controllers\Middleware;

use HttpStack\Template\Template;
use HttpStack\DocEngine\DocEngine;
class TemplateInit{
    protected $template;
    public function __construct(){
        $container = box();
        //dd($container);
        $this->template = $container->make("template");

        //dd($model->getModel());
        //$this->model = $container->make();
        //dd($this->template);
    }
    public function process($req,$res,$container){
        
        $res->setHeader("Template", 'passed');
        $this->template->set("appName", "httpStack");
        $template = $this->template;
        $container->singleton("template", function() use($template){
            return $template;
        });
        //$res->setBody($this->template);
        
        //$model = $container->make("template.model");
        //consoleLog($this->template->getCachedFile("base"));
        /*
        $base['appName'] = "HTtpstACK";
        $model->set("base", $base);
        $model->save();
       */
        //dd($model);

    }
}
?>