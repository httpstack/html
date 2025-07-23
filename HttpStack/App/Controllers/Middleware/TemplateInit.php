<?php
namespace HttpStack\App\Controllers\Middleware;

use Dev\Template\Template; 
use HttpStack\IO\FileLoader; 
use HttpStack\App\Views\View;

class TemplateInit
{
    protected string $template; 
    protected FileLoader $fileLoader; 

    public function __construct()
    {

    }

    /**
     * Processes the request and modifies the template.
     *
     * @param mixed $req The request object.
     * @param mixed $res The response object.
     * @param mixed $container The dependency injection container.
     * @return void
     */
    public function process($req, $res, $container)
    {
        $template = $container->make(Template::class);
        $html = $template->saveHTML();
        //oopen the base template and just put the html into the body
        //$res->setHeader("MW Set Base", "base");
        $res->setBody($html);
    }//pub
}