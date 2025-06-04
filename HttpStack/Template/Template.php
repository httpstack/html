<?php 
namespace HttpStack\Template;

use DOMDocument;
use DOMXPath;
use HttpStack\IO\FileLoader;

/**
 * Splice Class
 * 
 * Wire templates together with this easy to use template engine.
 * Load your html files into the engine.
 * Perform HTML/DOM related tasks on 
 */

 class Template{
    protected array $fileCache = [];
    protected FileLoader $fileLoader;
    public string $defaultFileExt = "html";
    protected DOMXPath $liveDocument;
    protected DOMDocument $dom;
    public function __construct(){
        $this->fileLoader = box("fileLoader");
    }
    public function readFile(string $nameSpace, string $baseFileName){
        //read a file contents into the $fileCache at $nameSpace => $html
        $this->fileCache[$nameSpace] = $this->fileLoader->readFile($baseFileName);
        return $this->fileCache[$nameSpace];
        //dd($this->fileCache);
    }
    public function getCachedFile(string $nameSpace):string{
        return $this->fileCache[$nameSpace];
    }
    public function loadLiveDoc(string $nameSpace){
        $binOptions = LIBXML_HTML_NODEFDTD|LIBXML_NOERROR|LIBXML_NOWARNING;
        $this->dom = new DOMDocument("1.0","utf-8");
        $this->dom->loadHTML($this->fileCache[$nameSpace], $binOptions);
        $this->liveDocument = new DOMXPath($this->dom);
    }
    public function normalizeHtml(string $html): string
    {
        // Ensure doctype and <html> tags
        if (stripos($html, '<!doctype') === false) {
            $html = "<!DOCTYPE html>\n" . $html;
        }
        if (stripos($html, '<html') === false) {
            $html = "<html>\n" . $html;
        }
        if (stripos($html, '</html>') === false) {
            $html .= "\n</html>";
        }

        // Load into DOM to manipulate structure
        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Ensure <head>
        $head = $dom->getElementsByTagName('head')->item(0);
        if (!$head) {
            $head = $dom->createElement('head');
            $dom->documentElement->insertBefore($head, $dom->documentElement->firstChild);
        }

        // Ensure <title>
        $titleTag = null;
        foreach ($head->getElementsByTagName('title') as $t) {
            $titleTag = $t;
            break;
        }
        if (!$titleTag) {
            $titleTag = $dom->createElement('title');
            $head->appendChild($titleTag);
        } else {
            $titleTag->textContent = "";
        }

        // Ensure <body>
        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body) {
            $body = $dom->createElement('body');
            $dom->documentElement->appendChild($body);
        }

        // Move stray nodes into <body>
        $htmlNode = $dom->documentElement;
        $toMove = [];
        foreach (iterator_to_array($htmlNode->childNodes) as $node) {
            if (
                $node->nodeType === XML_ELEMENT_NODE &&
                !in_array($node->nodeName, ['head', 'body'])
            ) {
                $toMove[] = $node;
            }
            if ($node->nodeType === XML_TEXT_NODE && trim($node->nodeValue) !== '') {
                $toMove[] = $node;
            }
        }
        foreach ($toMove as $node) {
            $body->appendChild($node);
        }

        libxml_clear_errors();
        return $dom->saveHTML();
    }
 }
?>