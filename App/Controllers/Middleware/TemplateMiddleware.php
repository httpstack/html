<?php
namespace App\Controllers\Middleware;
use App\_Template;
use App\Template;
use App\IO\FileLoader;
class TemplateMiddleware
{
    public $container;
    protected $templatePath;
    public $response;
    public $request;


    public function init($request, $response, $container, $next)
    {
        // Initialize the template engine
        
        $strBasePath = $container->settings['paths']['basePath']; 
        $strAssetDir = $strBasePath . $container->settings['paths']['assetUri'];
        $strViewDir = $strBasePath . $container->settings['paths']['viewPath'];
        

        $template = new _Template($container);
        $rendered = $template->render("/var/www/html/App/Views/Templates/template.master.html");
        //$template->inject("view.html",$rendered)
        $response->setBody($rendered);


        //$template->storeDoc("base", $rendered); 
        /*
        $node = $template->getByAttributeContains('data-template', 'nav');
        $nodeNav = $template->arrToUl($container->settings['navLinks'], false);
        $node->appendChild($nodeNav);
        $container->bind('template', $template);
*/
        // Call the next middleware or controller
        $next($request);
    }
}
?>