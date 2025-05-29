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
        
        $arrPaths = $container->settings['paths'];
        $strAssetsPath = $arrPaths['basePath'] . $arrPaths['assetsPath'];

        $arrConfig = [
            'uri'=>$arrPaths['assetsUri'], 
            'assets' => $container->settings['assets'],
            'required'=> $container->settings['required']
        ];
        $template = new Template($arrPaths['baseTemplate'], true);
        $template->bindAssets($arrConfig);
        /*
        $template->addFunction("appendAssets", function($assetsPath) use($arrAssets) {
            foreach($arrAssets as $asset){
                $type = pathinfo($asset, PATHINFO_EXTENSION);
                switch($type){
                    case "css":

                    break;

                    case "js":

                    break;

                    case "font":

                    break;

                    case "svg": 
                    case "jpg": 
                    case "gif":

                    break;
                }
            }
        });
        */
        $template->setData(['appTitle' => $container->settings['appTitle']]);
        $container->addProperty('template', $template); 


        //have a target node in your html that has an id="data-view" or whatever xpath u wish
        //convert css selector to an xpath query
        $xp = $template->css2xpath("#data-view");
        //get the DOMElement
        $viewSlot = $template->_findOne($xp);
        
        //alternatively you can use getElementById in this case
        $viewSlotAlt = $template->getElementById("data-view");
        
        $container->addProperty('viewContainer', $viewSlotAlt);       


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