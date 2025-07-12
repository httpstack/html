<?php

namespace Dev\Template;

use \DOMXPath;
use \DOMElement;
use \DOMDocument;
use \DOMDocumentFragment;
use Dev\Template\Contracts\TemplateInterface;

class Template implements TemplateInterface
{
    private static ?self $instance = null;
    protected string $templateContent = '';
    protected array $variables = [];
    protected ?DOMDocument $domDocument = null;

    public function __construct() {}
    private function __clone() {}
    public function __wakeup() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function load(string $templatePath): void
    {
        if (!file_exists($templatePath)) {
            throw new \InvalidArgumentException("Template file not found: $templatePath");
        }
        $this->templateContent = file_get_contents($templatePath);
        $this->domDocument = null;
    }

    public function setVariables(array $variables): void
    {
        $this->variables = array_merge($this->variables, $variables);
    }

    public function render(): string
    {
        if ($this->domDocument instanceof DOMDocument) {
            $output = $this->domDocument->saveHTML();
        } else {
            $output = $this->templateContent;
        }

        foreach ($this->variables as $key => $value) {
            if (is_string($value)) {
                $output = str_replace('{{' . $key . '}}', $value, $output);
            }
        }
        return $output;
    }

    /**
     * Returns a DOMDocument object representing the template content.
     * Ensures the content is wrapped in a full HTML structure for consistent parsing.
     *
     * @return DOMDocument The DOMDocument object.
     */
    public function getDom(): DOMDocument
    {
        if ($this->domDocument === null) {
            $this->domDocument = new DOMDocument("1.0", "utf-8");

            // Define common LIBXML options
            $options = LIBXML_NOERROR | LIBXML_NOWARNING; // Suppress errors and warnings

            // Check if the template content already has a full HTML structure
            // This is a simple check; more robust parsing might involve regex or a dedicated HTML parser.
            $hasFullHtmlStructure = (
                str_starts_with(trim($this->templateContent), '<!DOCTYPE') ||
                str_starts_with(trim($this->templateContent), '<html')
            );

            $contentToLoad = $this->templateContent;

            // If the content is a fragment, wrap it in a full HTML structure
            if (!$hasFullHtmlStructure) {
                // Ensure there's a head and body for consistent insertion points
                $contentToLoad = '<!DOCTYPE html><html><head></head><body>' . $this->templateContent . '</body></html>';
                // When we provide the full structure, LIBXML_HTML_NOIMPLIED is not needed
                // and can sometimes cause issues if we want a fully compliant document.
            } else {
                // If it already has a full structure, we can still use LIBXML_HTML_NOIMPLIED
                // if we expect it to be a partial document that might not have a body/head explicitly.
                // However, for consistency and to avoid unexpected moves, it's often better
                // to ensure the template itself is well-formed or wrap it.
                // For now, we'll remove LIBXML_HTML_NOIMPLIED if we're wrapping, otherwise keep it if not.
                // Given the goal is to prevent elements moving, ensuring a full structure is key.
                $options |= LIBXML_HTML_NOIMPLIED; // Keep this if content *might* be a fragment even if it has <html>
            }


            // Use @ to suppress any remaining warnings from loadHTML
            @$this->domDocument->loadHTML($contentToLoad, $options);
            $this->domDocument->normalizeDocument();
        }
        return $this->domDocument;
    }

    public function setDom(DOMDocument $dom): void
    {
        $this->domDocument = $dom;
    }

    public function getXPath(DOMDocument $dom): DOMXPath
    {
        return new DOMXPath($dom);
    }

    public function has(string $key): bool
    {
        return str_contains($this->templateContent, '{{' . $key . '}}');
    }

    public function get(string $key): mixed
    {
        return $this->variables[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->variables[$key] = $value;
    }

    public function __toString()
    {
        if ($this->domDocument instanceof DOMDocument) {
            return $this->domDocument->saveHTML();
        }
        return $this->templateContent;
    }

    public function importView(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("View file not found: $filePath");
        }

        $viewContent = file_get_contents($filePath);
        if ($viewContent === false || $viewContent === '') {
            throw new \InvalidArgumentException("View file is empty or could not be read: $filePath");
        }

        $dom = $this->getDom();

        $fragment = $dom->createDocumentFragment();

        $tempDom = new DOMDocument();
        @$tempDom->loadHTML($viewContent, LIBXML_HTML_NOIMPLIED | LIBXML_NOERROR | LIBXML_NOWARNING);

        foreach ($tempDom->childNodes as $node) {
            if ($node->nodeType === XML_ELEMENT_NODE || $node->nodeType === XML_TEXT_NODE) {
                $importedNode = $dom->importNode($node, true);
                $fragment->appendChild($importedNode);
            }
        }

        $xpath = $this->getXPath($dom);
        $targetElements = $xpath->query("//*[@data-key='view']");

        if ($targetElements->length === 0) {
            throw new \RuntimeException("Target element with attribute data-key='view' not found in the template.");
        }

        /** @var DOMElement $targetElement */
        $targetElement = $targetElements->item(0);

        while ($targetElement->hasChildNodes()) {
            $targetElement->removeChild($targetElement->firstChild);
        }

        $targetElement->appendChild($fragment);
    }
}
