<?php
namespace HttpStack\App\Views;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\IO\FileLoader;
use HttpStack\Template\Template;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\TemplateModel;

class View {

    protected Template $template;
    protected string $route;

    public function __construct(Request $req, Response $res, Container $container){
        // make sure templateModel is foing the logic of preparing the model since 
        // it is the concrete model 
        //box(abstract) is a helper for $container->make(abstract);
        $assetTypes = ["js", "css", "woff", "woff2", "otf", "ttf", "jpg", "jsx"];
        $this->template = $container->make("template");
        $fl = $container->make(FileLoader::class);
        $assets = $fl->findFilesByExtension($assetTypes, null);
        $this->template->bindAssets($assets);
        /*
        //example of defining a function with parameter
        $this->template->define("myFunc", function($myparam){
            return $myparam;
        });
        */
    }
    public function setRoute(string $route){
        $this->route = $route;
    }
    public function getRoute(){
        return $this->route;
    }
    public function getTemplate(){
        return $this->template;
    }
    public function importView(string $filePath){
        $this->template->importView($filePath);
    }
}
?>