<?php
namespace HttpStack\App\Views;


use \DocumentFragment;
use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\IO\FileLoader;
use HttpStack\Template\Template;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\TemplateModel;

class View {

    protected Template $template;
    protected string $view;
    protected Container $container;

    public function __construct(Request $req, Response $res, Container $container){
        // make sure templateModel is foing the logic of preparing the model since 
        // it is the concrete model 
        //box(abstract) is a helper for $container->make(abstract);
        $this->container = $container;
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
    public function objectifyView($view){
        $fl = $this->container->make(FileLoader::class);
        $viewPath = $fl->findFile($view, "routeViewsDir", "html");
        $htmView = $fl->readFile($view);
        $frag = $this->template->createDocumentFragment();
        $frag->append($htmView);        
        $d = new \DOMDocument();
        libxml_get_errors();
        @$d->loadHTML($htmView);
        libxml_clear_errors();
        $d->append($frag);
        
        return $frag;
    }
    public function setView(string $view){
        $viewNode = $this->objectifyView($view);

    }
    public function getView(){
        return $this->view;
    }
    public function getTemplate(){
        return $this->template;
    }
    public function importView(string $filePath){
        $this->template->importView($filePath);
    }
}
?>