<?php
namespace HttpStack\DocEngine;

use HttpStack\IO\FileLoader;
use HttpStack\Traits\Template;
use HttpStack\Traits\DomQuery;
use DOMDocument;
use DOMDocumentFragment;
use DOMElement;
use DOMXPath;
use DOMNode;

class DocEngine extends DOMDocument
{

    use Template;
    use DomQuery;

    // --- Internals

    protected DOMXPath $docMap;
    public string $defaultFileType = "html";
    protected FileLoader $fileLoader;

    public function __construct(string $html=null,protected string $title='')
    {
        parent::__construct('1.0', 'UTF-8');
        $html = $html ? $html : "<!-- -->";
        $this->fileLoader = new FileLoader();
        libxml_use_internal_errors(true);
        $html = $this->startDocument($html);   
        $this->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        @$this->xpath = new DOMXPath($this);
    }
public function startDocument(string $html, string $title = 'Document'): string
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
        $titleTag = $dom->createElement('title', htmlspecialchars($title));
        $head->appendChild($titleTag);
    } else {
        $titleTag->textContent = htmlspecialchars($title);
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
    /**
     * Abstraction of FileLoader::mapDirectory
     * 
     * @param string $namespace
     * @param string $path
     * @return void
     */
    public function addSourcePath(string $namespace, string $path):self{
        $this->fileLoader->mapDirectory($namespace,$path);
        return $this;
    }
    // --- Load a file
    public function docFromFile(string $file): self
    {
        $file = $this->fileLoader->findFile($file, null, 'html' );
        $html = file_get_contents($file);
        $title = $this->title ? $this->title : app()->getSettings()['appName'];
        $this->__construct($html, $title);
        return $this;
    }
    public function docToHTML(?DOMNode $context = null): string{
        return $context ? $this->saveHTML($context) : $this->saveHTML();
    }
    public function docToFile(string $destFile, ?DOMNode $context = null):self{
        $html = $context ? $this->saveHTML($context) : $this->saveHTML();
        file_put_contents($destFile, $html);
        return $this;
    }



}