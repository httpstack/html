<?php
namespace HttpStack\App\Controllers\Middleware;

use HttpStack\Http\Request; 
use HttpStack\Http\Response; 
use HttpStack\IO\FileLoader;
use HttpStack\App\Views\View;
use HttpStack\Template\Template;
use HttpStack\Container\Container;

class TemplateInit
{
    protected Template $template; 
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
    public function process(Request $req, Response $res, Container $container)
    {
        $v = new View($req, $res, $container);
        //register the view namespace agian, returning this view
        // that has the template object within it.
        $container->singleton("view", function(Container $c, string $route) use($v){
          $v->setRoute($route);
          return $v;
        });



        //$html = $template->render();

        //var_dump($template);
        //$html = $template->saveHTML();
        //oopen the base template and just put the html into the body
        //$res->setHeader("MW Set Base", "base");
        //$res->setBody();
    }//pub
}