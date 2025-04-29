<?php
namespace App\Traits;
use \DOMXPath;
use \DOMDocument;
use \DOMNode;
use \DOMElement;
use \DOMNodeList;

trait DomTemplateUtils
{
    protected DOMXPath $xpath;
    protected array $functions = [];

    protected function initXPath(): void
    {
        $this->xpath = new DOMXPath($this);
    }

    public function loadFromFile(string $filename): void
    {
        $this->loadHTMLFile($filename);
        $this->initXPath();
    }

    public function loadFromString(string $html): void
    {
        $this->loadHTML($html);
        $this->initXPath();
    }

    public function render(bool $pretty = true): string
    {
        $this->formatOutput = $pretty;
        return $this->saveHTML();
    }

    public function find(string $query): ?DOMNodeList
    {
        return $this->xpath->query($query);
    }

    public function createElementNode(string $tagName, string $textContent = '', array $attributes = []): DOMElement
    {
        $element = $this->createElement($tagName, $textContent);

        foreach ($attributes as $attr => $value) {
            $element->setAttribute($attr, $value);
        }

        return $element;
    }

    public function appendTo(string $query, DOMNode $node): void
    {
        $targets = $this->find($query);
        if ($targets) {
            foreach ($targets as $target) {
                $target->appendChild($node->cloneNode(true));
            }
        }
    }

    public function appendFirst(string $query, DOMNode $node): void
    {
        $targets = $this->find($query);
        if ($targets && $targets->length > 0) {
            $targets->item(0)->appendChild($node);
        }
    }

    public function appendMultiple(string $query, array $nodes): void
    {
        $targets = $this->find($query);
        if ($targets) {
            foreach ($targets as $target) {
                foreach ($nodes as $node) {
                    $target->appendChild($node->cloneNode(true));
                }
            }
        }
    }

    public function replaceFirst(string $query, DOMNode $newNode): void
    {
        $targets = $this->find($query);
        if ($targets && $targets->length > 0) {
            $oldNode = $targets->item(0);
            $oldNode->parentNode->replaceChild($newNode, $oldNode);
        }
    }

    public function deleteNode(string $query): void
    {
        $nodes = $this->find($query);
        if ($nodes) {
            foreach ($nodes as $node) {
                $node->parentNode->removeChild($node);
            }
        }
    }

    public function setTextContent(string $query, string $text): void
    {
        $nodes = $this->find($query);
        if ($nodes) {
            foreach ($nodes as $node) {
                $node->textContent = $text;
            }
        }
    }

    public function setAttribute(string $query, string $attrName, string $value): void
    {
        $nodes = $this->find($query);
        if ($nodes) {
            foreach ($nodes as $node) {
                if ($node instanceof DOMElement) {
                    $node->setAttribute($attrName, $value);
                }
            }
        }
    }

    // --- Template Variable and Function Engine ---

    public function addFunction(string $name, callable $callback): void
    {
        $this->functions[$name] = $callback;
    }

    public function parseTemplate(array $data = []): void
    {
        // Step 1: Render the current HTML
        $html = $this->saveHTML();
    
        // Step 2: Handle {{ key }} simple replacements with str_replace
        $search = [];
        $replace = [];
    
        foreach ($data as $key => $value) {
            $search[] = '{{ ' . $key . ' }}';
            $replace[] = $value;
        }
    
        $html = str_replace($search, $replace, $html);
    
        // Step 3: Handle {{ functionName(param1, param2) }} with preg_match_all
        if (preg_match_all('/\{\{\s*(\w+)\((.*?)\)\s*\}\}/', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $fullMatch = $match[0]; // e.g., {{ myFunction(param1, param2) }}
                $funcName = $match[1];
                $paramString = $match[2];
    
                $params = array_map('trim', explode(',', $paramString));
    
                if (isset($this->functions[$funcName])) {
                    $replacement = call_user_func_array($this->functions[$funcName], $params);
                    $html = str_replace($fullMatch, $replacement, $html);
                }
            }
        }
    
        // Step 4: Reload into DOM
        $this->loadHTML($html);
        $this->initXPath(); // reinit xpath because DOM reloaded
    }
    

    public function watch(string $query, callable $callback): void
    {
        $nodes = $this->find($query);
        if ($nodes) {
            foreach ($nodes as $node) {
                $callback($node);
            }
        }
    }
}
