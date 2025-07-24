<?php
namespace HttpStack\App\Controllers\Middleware;

use HttpStack\IO\FileLoader; 
use HttpStack\App\Views\View; 
use HttpStack\Template\Template;

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
    public function process($req, $res, $container)
    {
        $assetTypes = ["js", "css", "woff", "woff2", "otf", "ttf", "jpg", "jsx"];
        $template = $container->make("template");
        $fl = $container->make(FileLoader::class);
        $assets = $fl->findFilesByExtension($assetTypes, null);
        $template->bindAssets($assets);     


        $html = $template->render();
        //var_dump($template);
        //$html = $template->saveHTML();
        //oopen the base template and just put the html into the body
        //$res->setHeader("MW Set Base", "base");
        $res->setBody($html);
    }//pub
}