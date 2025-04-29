<?php
namespace App\Traits;
use \DOMDocument;
use \DOMXPath;
use \DOMNode;
use \DOMNodeList;
use \DOMElement;
use \DOMDocumentFragment;
use \DOMText;

use \LogicException;
use \Exception;

/**
 * Trait DomHelper
 * Helper methods to manipulate the DOM more easily with extended capabilities.
 * Must be used inside a class that extends DOMDocument.
 */
trait DomHelper
{
    /** @var DOMXPath $xpath Cached XPath engine */
    protected DOMXPath $xpath;

    /** Initialize the XPath engine */
    protected function initXpath(): void
    {
        $this->xpath = new DOMXPath($this);
    }

    /**
     * Find elements using XPath query
     */
    public function domHelper_find(string $query, ?DOMNode $contextNode = null): DOMNodeList
    {
        return $this->xpath->query($query, $contextNode);
    }

    /**
     * Find the first matching element using XPath
     */
    public function domHelper_findOne(string $query, ?DOMNode $contextNode = null): ?DOMNode
    {
        $nodes = $this->xpath->query($query, $contextNode);
        return ($nodes && $nodes->length > 0) ? $nodes->item(0) : null;
    }

    /**
     * Create element with optional text node
     */
    public function domHelper_createElementWithText(string $name, ?string $text = null): DOMElement
    {
        $el = $this->createElement($name);
        if ($text !== null) {
            $el->appendChild($this->createTextNode($text));
        }
        return $el;
    }

    /**
     * Append child to parent node
     */
    public function domHelper_append(DOMNode $parent, DOMNode $child): DOMNode
    {
        return $parent->appendChild($child);
    }

    /**
     * Replace old node with new node
     */
    public function domHelper_replace(DOMNode $oldNode, DOMNode $newNode): void
    {
        $oldNode->parentNode?->replaceChild($newNode, $oldNode);
    }

    /**
     * Remove a node from the DOM
     */
    public function domHelper_delete(DOMNode $node): void
    {
        $node->parentNode?->removeChild($node);
    }

    /**
     * Clone a node deeply
     */
    public function domHelper_clone(DOMNode $node): DOMNode
    {
        return $node->cloneNode(true);
    }

    /**
     * Import and append nodes matching XPath from another DOMDocument
     */
    public function domHelper_appendFragment(DOMDocument $sourceDoc, string $query, DOMNode $target): void
    {
        $sourceXpath = new DOMXPath($sourceDoc);
        $nodes = $sourceXpath->query($query);
        foreach ($nodes as $node) {
            $imported = $this->importNode($node, true);
            $target->appendChild($imported);
        }
    }

    /**
     * Set inner HTML of a DOM element
     */
    public function domHelper_setInnerHtml(DOMElement $element, string $html): void
    {
        while ($element->firstChild) {
            $element->removeChild($element->firstChild);
        }

        $tmp = new DOMDocument();
        $tmp->loadHTML("<div>$html</div>", LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        foreach ($tmp->documentElement->childNodes as $child) {
            $imported = $this->importNode($child, true);
            $element->appendChild($imported);
        }
    }

    /**
     * Get inner HTML content of an element
     */
    public function domHelper_getInnerHtml(DOMNode $element): string
    {
        $html = '';
        foreach ($element->childNodes as $child) {
            $html .= $element->ownerDocument->saveHTML($child);
        }
        return $html;
    }

    /**
     * Dump the current DOM tree to output
     */
    public function domHelper_dumpTree(): void
    {
        echo $this->saveHTML();
    }

    /**
     * Return DOM tree as string
     */
    public function domHelper_getTree(): string
    {
        return $this->saveHTML();
    }

    /**
     * Create a document fragment from HTML string
     */
    public function domHelper_createFragment(string|DOMElement $html): DOMDocumentFragment
    {
        if($html instanceof DOMElement){
            $html = $this->importNode($html, true);
        }
        $fragment = $this->createDocumentFragment();
        @$fragment->appendChild($html);
        return $fragment;
    }

    /**
     * Create and append a stylesheet link in head
     */
    public function domHelper_addStylesheet(string $href): void
    {
        $link = $this->createElement('link');
        $link->setAttribute('rel', 'stylesheet');
        $link->setAttribute('href', $href);

        $head = $this->getElementsByTagName('head')->item(0);
        if ($head) {
            $head->appendChild($link);
        }
    }

    /**
     * Create and append a script in body
     */
    public function domHelper_addScript(string $src, bool $defer = true): void
    {
        $script = $this->createElement('script');
        $script->setAttribute('src', $src);
        if ($defer) {
            $script->setAttribute('defer', 'defer');
        }

        $body = $this->getElementsByTagName('body')->item(0);
        if ($body) {
            $body->appendChild($script);
        }
    }

    /**
     * Create and append preload link (e.g., for fonts/images)
     */
    public function domHelper_addPreload(string $href, string $as): void
    {
        $link = $this->createElement('link');
        $link->setAttribute('rel', 'preload');
        $link->setAttribute('href', $href);
        $link->setAttribute('as', $as);

        $head = $this->getElementsByTagName('head')->item(0);
        if ($head) {
            $head->appendChild($link);
        }
    }

    /**
     * Watch a DOMNode for mutation (placeholder, JS polyfill would be required)
     * This is a conceptual method.
     */
    public function domHelper_watchNode(DOMNode $node, callable $callback): void
    {
        // Placeholder — watching DOM mutations in PHP is not possible.
        // You'd have to export data to JS MutationObserver in frontend context.
        throw new LogicException("Watching DOM nodes is only supported in frontend (JS).");
    }

    /**
     * Load HTML into the document safely
     */
    public function domHelper_loadSafeHTML(string $html): void
    {
        libxml_use_internal_errors(true);
        $this->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
    }

    /**
     * Create element with attributes
     */
    public function domHelper_createElementWithAttrs(string $name, array $attrs = []): DOMElement
    {
        $el = $this->createElement($name);
        foreach ($attrs as $attr => $value) {
            $el->setAttribute($attr, $value);
        }
        return $el;
    }
}
