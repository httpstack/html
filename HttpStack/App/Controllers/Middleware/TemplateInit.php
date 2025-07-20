<?php
namespace HttpStack\App\Controllers\Middleware;

use Dev\Template\Template; 
use HttpStack\IO\FileLoader; 

class TemplateInit
{
    protected Template $template; 
    protected FileLoader $fileLoader; 

    public function __construct()
    {
        $container = box(); 
        
        
        $this->template = $container->make("template");
        $this->fileLoader = $container->make("fileLoader");
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
        // 1.)  Pass the array of assets you want URLS for to the fileloader
        //      put generated links array into bindAssets
        $assetExtensions = ["js","css","woff","woff2","odt","ttf","jpg"];
        $assets = $this->fileLoader->findFilesByExtension($assetExtensions);
        

        // 2.)  IMPORTANT: Set Templates data array to the base.json file for replace vars.
        //      And the array of links for the main nav bar
        $model = $container->make("template.model");
        $mainLinks = $model->getLinks("main");
        $model->set("links", $mainLinks);
        $model->set("assets", $assets);
        $this->template->setVariables($model->getAll()['base.json']);
        

        $this->template->define("myFunc", function($myparam){
            return $myparam;
        });
        // 3.) IMPORTANT: Re-bind the modified template instance to the container as a singleton.
        $template = $this->template;
        $container->singleton("template", function() use($template){
            return $template;
        });
    }//pub
}