<?php
namespace HttpStack\App\Controllers\Middleware;

use Dev\Template\Template; // Assuming this is your Template class
use HttpStack\IO\FileLoader; // Your new FileLoader class

class TemplateInit
{
    protected Template $template; // Type hint for clarity
    protected FileLoader $fileLoader; // Type hint for clarity

    public function __construct()
    {
        $container = box(); // Assuming 'box()' returns your dependency injection container
        // Make sure "template" is correctly bound in your container to an instance
        // of HttpStack\Template\Template that has loaded the initial HTML content.
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

        // Get the model that holds your asset data (e.g., assets.json) and replacement data.
        $model = $container->make("template.model");

        // --- DOM Manipulation for Asset Injection ---
        $dom = $this->template->getDom();
        $xpath = $this->template->getXPath($dom);

        $objHead = $xpath->query("//head")->item(0);
        $objBody = $xpath->query("//body")->item(0); // Get the body element for script insertion

        // Array to collect image URLs for the preloader
        $imagePreloadUrls = [];

        // Check if <head> exists before proceeding to add assets
        if ($objHead === null) {
            error_log("CRITICAL WARNING: <head> tag NOT FOUND in the template. Links and fonts will not be added to head.");
        }
        // Check if <body> exists for script injection
        if ($objBody === null) {
            error_log("CRITICAL WARNING: <body> tag NOT FOUND in the template. Scripts and image preloader will not be added to body.");
        }

        // --- Crucial Check for assets.json ---
        $assetsData = $model->get("assets.json");

        if (!is_array($assetsData) && !$assetsData instanceof \Traversable) {
            error_log("ERROR: \$model->get('assets.json') did not return an iterable type. Received type: " . gettype($assetsData));
            $assetsData = []; // Default to an empty array to prevent fatal errors
        }

        // Iterate through assets, treating $objAsset as an associative array
        // Simplified: removed $intIndex as it's not used
        foreach ($assetsData as $objAsset) {
            $strType = $objAsset['type'] ?? ''; // Access type using array key
            $filename = $objAsset['filename'] ?? ''; // Get filename from asset data

            // Determine file extension from filename for findFile
            $strFileExt = pathinfo($filename, PATHINFO_EXTENSION);

            // Use FileLoader to find the absolute path.
            // Ensure $absoluteSrc is always a string, even if findFile returns NULL.
            $absoluteSrc = $this->fileLoader->findFile($filename, null, $strFileExt) ?? '';

            // Ensure DOC_ROOT is defined and correctly removes the absolute path prefix.
            // $src will now always be a string, preventing the deprecation warning.
            $src = str_replace(DOC_ROOT, "", $absoluteSrc);

            // Only proceed if $src is not empty after path resolution
            if (empty($src)) {
                error_log("DEBUG: Skipped asset '" . $filename . "' of type '" . $strType . "' because resolved SRC is empty.");
                continue; // Skip to the next asset if src is empty
            }

            switch ($strType) {
                case "sheet":
                    if ($objHead) {
                        $objLink = $dom->createElement("link");
                        $objLink->setAttribute("rel", "stylesheet");
                        $objLink->setAttribute("href", $src);
                        if (isset($objAsset['media'])) {
                            $objLink->setAttribute("media", $objAsset['media']);
                        }
                        $objHead->appendChild($objLink);
                        error_log("DEBUG: Added stylesheet link to HEAD: " . $src);
                    } else {
                        error_log("DEBUG: Skipped adding stylesheet link to HEAD (HEAD not found): " . $src);
                    }
                    break;

                case "script":
                    // Scripts are explicitly placed in the body if it exists
                    if ($objBody) {
                        $objScript = $dom->createElement("script");
                        $objScript->setAttribute("src", $src);
                        if (isset($objAsset['async']) && $objAsset['async']) {
                            $objScript->setAttribute("async", "true");
                        }
                        if (isset($objAsset['defer']) && $objAsset['defer']) {
                            $objScript->setAttribute("defer", "true");
                        }
                        $objBody->appendChild($objScript);
                        error_log("DEBUG: Added script to BODY: " . $src);
                    } else {
                        error_log("DEBUG: Skipped adding script to BODY (BODY not found): " . $src);
                    }
                    break;

                case "font":
                    if ($objHead) { // $src is already checked for emptiness above
                        // Check if it's a Google Fonts URL
                        if (str_contains($src, 'fonts.googleapis.com') || str_contains($src, 'fonts.gstatic.com')) {
                            $objLink = $dom->createElement("link");
                            $objLink->setAttribute("rel", "stylesheet");
                            $objLink->setAttribute("href", $src);
                            $objHead->appendChild($objLink);
                            error_log("DEBUG: Added Google Font link to HEAD: " . $src);
                        } else {
                            // Assume it's a local font file, use preload
                            $extension = pathinfo($src, PATHINFO_EXTENSION);
                            if (in_array($extension, ['woff', 'woff2', 'ttf', 'otf'])) {
                                $objLink = $dom->createElement("link");
                                $objLink->setAttribute("rel", "preload");
                                $objLink->setAttribute("href", $src);
                                $objLink->setAttribute("as", "font");
                                $objLink->setAttribute("type", "font/{$extension}"); // e.g., font/woff2
                                $objLink->setAttribute("crossorigin", "anonymous"); // Required for font preloading
                                $objHead->appendChild($objLink);
                                error_log("DEBUG: Added local font preload link to HEAD: " . $src);
                            } else {
                                error_log("DEBUG: Unsupported font extension for preload: " . $src);
                            }
                        }
                    } else {
                        error_log("DEBUG: Skipped adding font link (HEAD not found): " . $src);
                    }
                    break;

                case "image":
                    if (!empty($src)) { // $src is already checked for emptiness above
                        $imagePreloadUrls[] = $src; // Collect image URLs
                        error_log("DEBUG: Collected image for preloading: " . $src);
                    } else {
                        error_log("DEBUG: Skipped collecting empty image src.");
                    }
                    break;

                default:
                    error_log("DEBUG: Unknown asset type encountered: " . $strType);
                    break;
            }
        }

        // Generate and append image preloader script to the body
        if ($objBody && !empty($imagePreloadUrls)) {
            $preloaderScriptContent = "
                (function() {
                    var imagesToPreload = " . json_encode($imagePreloadUrls) . ";
                    imagesToPreload.forEach(function(url) {
                        var img = new Image();
                        img.src = url;
                        // Optional: add event listeners for loaded/error if needed
                        // img.onload = function() { console.log('Preloaded: ' + url); };
                        // img.onerror = function() { console.error('Failed to preload: ' + url); };
                    });
                })();
            ";

            $objScript = $dom->createElement("script");
            $objScript->nodeValue = $preloaderScriptContent; // Use nodeValue for script content
            $objBody->appendChild($objScript);
            error_log("DEBUG: Added image preloader script to BODY.");
        } else if (!empty($imagePreloadUrls)) {
            error_log("DEBUG: Skipped adding image preloader script (BODY not found).");
        }
        // --- End DOM Manipulation ---

        // 2. IMPORTANT: Send the modified DOMDocument back to the Template object.
        //    This updates the template's internal state with the changes.
        $this->template->setDom($dom);

        // --- DIAGNOSTIC STEP: Log the HTML after TemplateInit DOM manipulation ---
        error_log("--- Generated HTML after TemplateInit DOM manipulation ---");
        error_log($this->template->getDom()->saveHTML());
        error_log("---------------------------------------------------------");
        // --- END DIAGNOSTIC STEP ---


        // Here we can connect to the model and get the replacement data for the template
        // Using ['base.json'] as per your latest change
        $this->template->setVariables($model->getAll()['base.json']);

        // Re-bind the modified template instance to the container as a singleton.
        // This ensures that any subsequent requests for "template" from the container
        // will receive this modified instance.
        $template = $this->template;
        $container->singleton("template", function() use($template){
            return $template;
        });
    }
}
