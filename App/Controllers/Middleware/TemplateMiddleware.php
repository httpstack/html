<?php
namespace App\Controllers\Middleware;
use App\DOM\Template;

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
        

        $template = new Template($container->settings['paths']['baseTemplate'], true);
        $template->addFunction("doThis", function($arg) {
            return "Hello, " . htmlspecialchars($arg);
        });
        $template->setData(['title' => 'My Dynamic Page']);
        $template->setAssetsPath($strAssetDir);
        $container->addProperty('template', $template); 
        $container->addProperty('viewSlot', $template->getElementById("data-view"));       


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