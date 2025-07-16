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
        $res->setHeader("Template", 'passed');

        
        $model = $container->make("template.model");

        
        $dom = $this->template->getDom();
        $xpath = $this->template->getXPath($dom);

        $head = $xpath->query("//head")->item(0);
        $body = $xpath->query("//body")->item(0); // Get the body element for script insertion

        $imagePreloadUrls = [];
        $assetExtensions = ["js","css","woff","woff2","odt","ttf","jpg"];
        $assetPaths= $this->fileLoader->findFilesByExtension($assetExtensions);
        foreach ($assetPaths as $asset) {

            $strType = pathinfo($asset, PATHINFO_EXTENSION);
            $filename = pathinfo($asset, PATHINFO_BASENAME); 
            $src = str_replace(DOC_ROOT, "", $asset);
            $imagesToPreload = [];
            switch($strType){
                case "js":
                    $script = $dom->createElement("script");
                    $script->setAttribute("type", "text/javascript");
                    if(str_contains($filename, "babel")){
                        $script->setAttribute("type", "text/babel");
                    }
                    $script->setAttribute("src", $src);
                    $body->appendChild($script);
                break;

                case "css":
                    $link = $dom->createElement("link");
                    $link->setAttribute('type', 'text/css');
                    $link->setAttribute('rel', 'stylesheet');
                    $link ->setAttribute('href', $src);
                    $head->appendChild($link);
                break;

                case "woff":
                case "woff2":
                case "otf":
                case "ttf":
                    $link = $dom->createElement("link");
                    $link->setAttribute("rel", "preload");
                    $link->setAttribute("href", $src);
                    $link->setAttribute("as", "font");
                    $link->setAttribute("type", "font/{$strType}"); 
                    $link->setAttribute("crossorigin", "anonymous"); // Required for font preloading
                    $head->appendChild($link);
                break;

                case "jpg":
                    $imagesToPreload[] = $src;
                break;

            }

        }//foreach
        if(!empty($imagesToPreload)){
                $preloaderScriptContent = `
                (function() {
                    var imagesToPreload = " . json_encode($imagesToPreload) . ";
                    imagesToPreload.forEach(function(url) {
                        var img = new Image();
                        img.src = url;
                        // Optional: add event listeners for loaded/error if needed
                        img.onload = function() { console.log('Preloaded: ' + url); };
                        // img.onerror = function() { console.error('Failed to preload: ' + url); };
                    });
                })();
            `;

            $script = $dom->createElement("script");
            $script->nodeValue = $preloaderScriptContent; 
            $body->appendChild($script);
        }
        // 2. IMPORTANT: Send the modified DOMDocument back to the Template object.
        $this->template->setDom($dom);

        // 3. IMPORTANT: Set Templates data array to the base.json file for replace vars.
        $this->template->setVariables($model->getAll()['base.json']);
                $this->template->setVariables(["links" => [
                ['url' => 'https://example.com/home', 'text' => 'Home Page'],
                ['url' => 'https://example.com/about', 'text' => 'About Us'],
                ['url' => 'https://example.com/contact', 'text' => 'Contact']
            ]]);
        $this->template->define("myFunc", function($myparam){
            return $myparam;
        });
        // 4. IMPORTANT: Re-bind the modified template instance to the container as a singleton.
        $template = $this->template;
        $container->singleton("template", function() use($template){
            return $template;
        });
    }//pub
}
