<?php
namespace HttpStack\App\Controllers\Middleware;

use HttpStack\DocEngine\DocEngine;

class TemplateInit{
    protected DocEngine $template;
    public function __construct(){
        $container = box();
        //dd($container);
        $this->template = $container->make("templateInit");
        //dd($this->template);
    }
    public function process($req,$res,$container){
        dd($res);
        $res->setHeader("Content-Type", "text/html");
        $res->setHeader("Middleware", "Session Started");
        
        $res->setBody($this->template->docToHTML());
        $res->send();
    }
}
?>