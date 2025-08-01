<?php

namespace HttpStack\Template;

use \DOMXPath; // Corrected typo: DOMPXath -> DOMXPath
use DOMDocument;
use DocumentFragment; // Changed from Dom\DocumentFragment for native PHP DOM
use HttpStack\IO\FileLoader;
use HttpStack\Container\Container;
//use HttpStack\Traits\ProcessTemplate;
use HttpStack\App\Models\TemplateModel;

class Template extends DOMDocument
{
    use \HttpStack\Traits\ProcessTemplate;
    public ?DOMXPath $map; // Corrected type hint
    protected array $variables = [];
    protected Container $container;
    protected TemplateModel $model;

    public function __construct(string $baseTemplatePath,  TemplateModel $tm)
    {

        @$this->loadHTMLFile($baseTemplatePath, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING);
        $this->setVars($tm->getAll());
        $this->map = new DOMXPath($this);
    }

    public function setVars(array $vars)
    {
        $this->variables = $vars;
    }

    public function bindAssets(array $assets)
    {
        $head = $this->map->query("//head")[0];
        $body = $this->map->query("//body")[0];

        $imagesToPreload = []; // Renamed from imagePreloadUrls for consistency

        foreach ($assets as $asset) {
            $strType = pathinfo($asset, PATHINFO_EXTENSION);
            $filename = pathinfo($asset, PATHINFO_BASENAME);
            // $required check seems to be a boolean, assuming it's used to decide placement
            $required = str_contains($filename, "required");
            $src = str_replace(DOC_ROOT, "", $asset);

            switch ($strType) {
                case "js":
                    $script = $this->createElement("script");
                    $script->setAttribute("type", "text/javascript");
                    if (str_contains($filename, "babel")) {
                        $script->setAttribute("type", "text/babel");
                    }
                    $script->setAttribute("src", $src);
                    if ($required) {
                        $head->appendChild($script);
                    } else {
                        $body->appendChild($script);
                    }
                    break;

                case "jsx":
                    $script = $this->createElement("script");
                    $script->setAttribute("type", "text/babel");
                    $script->setAttribute("src", $src);
                    $body->appendChild($script); // JSX scripts usually go in body
                    break; // Added missing break

                case "css":
                    $link = $this->createElement("link");
                    $link->setAttribute('type', 'text/css');
                    $link->setAttribute('rel', 'stylesheet');
                    $link->setAttribute('href', $src);
                    $head->appendChild($link);
                    break;

                case "woff":
                case "woff2":
                case "otf":
                case "ttf":
                    $link = $this->createElement("link");
                    $link->setAttribute("rel", "preload");
                    $link->setAttribute("href", $src);
                    $link->setAttribute("as", "font");
                    $link->setAttribute("type", "font/{$strType}");
                    $link->setAttribute("crossorigin", "anonymous"); // Required for font preloading
                    $head->appendChild($link);
                    break;

                case "jpg":
                case "png": // Added png as well, common for preloading
                case "gif":
                case "svg":
                    $imagesToPreload[] = $src;
                    break;
            }
        } //foreach

        if (!empty($imagesToPreload)) {
            // Corrected string concatenation from backticks to standard PHP `.`
            $preloaderScriptContent = '
                (function() {
                    var imagesToPreload = ' . json_encode($imagesToPreload) . ';
                    imagesToPreload.forEach(function(url) {
                        var img = new Image();
                        img.src = url;
                        // Optional: add event listeners for loaded/error if needed
                        img.onload = function() { console.log("Preloaded: " + url); };
                        // img.onerror = function() { console.error("Failed to preload: " + url); };
                    });
                })();
            ';

            $script = $this->createElement("script");
            // Use createTextNode for script content to properly escape HTML entities if any
            $script->appendChild($this->createTextNode($preloaderScriptContent));
            $body->appendChild($script);
        }
        $this->setMap(); // Re-setting map after DOM modifications
    }

    /**
     * Inserts a DocumentFragment into the specified view placeholder.
     *
     * @param DOMDocumentFragment $viewFragment The fragment to insert.
     */
    public function insertView(DocumentFragment $viewFragment) // Changed type hint
    {
        $targetNodes = $this->map->query("//*[@data-key='view']");

        // Check if any target node was found
        if ($targetNodes->length > 0) {
            // Append the fragment to the first found target node.
            // Using appendChild on item(0) is robust and widely compatible.
            $targetNodes->item(0)->appendChild($viewFragment);
        } else {
            // Log an error if the target element is not found.
            // This prevents a fatal error if the 'view' placeholder is missing from the template.
            error_log("Template error: Target element with data-key='view' not found in the template. Cannot insert view fragment.");
            // Depending on your application's needs, you might choose to:
            // 1. Throw a more specific exception: throw new \RuntimeException("View placeholder not found.");
            // 2. Append to <body> as a fallback: $this->map->query("//body")[0]->appendChild($viewFragment);
        }
    }

    public function setMap(): DOMXPath
    {
        $this->map = new DOMXPath($this);
        return $this->map;
    }

    public function getMap(): DOMXPath
    {
        return $this->map;
    }
}
