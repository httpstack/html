<?php

namespace Dev\Template;

use \DOMXPath;
use \DOMElement;
use \DOMDocument;
use Dev\Core\Version;
use \DOMDocumentFragment;
use Dev\Template\Contracts\TemplateInterface; // Assuming this is the correct namespace for your Version class

class Template implements TemplateInterface
{
    private static ?self $instance = null;
    protected string $templateContent = '';
    protected array $variables = [];
    protected ?DOMDocument $domDocument = null;
    protected array $functions = []; // NEW: Property to store defined functions

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

    public static function getVersion(): string
    {
        return Version::getVersion();
    }

    public function load(string $templatePath): void
    {
        if (!file_exists($templatePath)) {
            throw new \InvalidArgumentException("Template file not found: $templatePath");
        }
        $this->templateContent = file_get_contents($templatePath);
        $this->domDocument = null; // Reset DOMDocument when new content is loaded
    }

    public function setVariables(array $variables): void
    {
        $this->variables = array_merge($this->variables, $variables);
    }

    /**
     * Defines a custom function that can be called within the template.
     *
     * @param string $name The name of the function as it will appear in the template (e.g., 'myFunc').
     * @param callable $callback The PHP callable (function, closure, array callable) to execute.
     * @return void
     */
    public function define(string $name, callable $callback): void
    {
        $this->functions[$name] = $callback;
    }

    /**
     * Renders the template content, processing data-repeat loops, variables, and function calls.
     */
    public function render(): string
    {
        // IMPORTANT: Start with the raw templateContent string if DOMDocument hasn't been used,
        // otherwise use the current state of the DOMDocument.
        $output = '';
        if ($this->domDocument instanceof DOMDocument) {
            $output = $this->domDocument->saveHTML();
        } else {
            $output = $this->templateContent;
        }

        // --- Step 1: Process data-repeat loops using DOMXPath ---
        // This part remains the same as it operates on the DOM structure.
        $dom = $this->getDom(); // Ensure DOM is loaded for XPath queries
        $xpath = $this->getXPath($dom);
        $repeatElements = $xpath->query("//*[@data-repeat]");

        // We need to iterate over a static list of elements because modifying the DOM
        // during iteration can cause issues with live NodeLists.
        $elementsToProcess = [];
        foreach ($repeatElements as $element) {
            $elementsToProcess[] = $element;
        }

        foreach ($elementsToProcess as $repeatElement) {
            /** @var DOMElement $repeatElement */
            $repeatAttr = $repeatElement->getAttribute('data-repeat');

            if (preg_match('/^([a-zA-Z0-9_]+)\s+as\s+([a-zA-Z0-9_]+)$/', $repeatAttr, $matches)) {
                $collectionVarName = $matches[1];
                $itemVarName = $matches[2];

                if (isset($this->variables[$collectionVarName]) && is_iterable($this->variables[$collectionVarName])) {
                    $collectionData = $this->variables[$collectionVarName];

                    // Find the single template item within this repeat block (e.g., the <li>)
                    // Use a new XPath query on the specific repeat element to ensure it's a child.
                    $itemTemplateElements = $xpath->query("*[@data-repeat-item='" . $itemVarName . "']", $repeatElement);

                    if ($itemTemplateElements->length > 0) {
                        /** @var DOMElement $itemTemplate */
                        $itemTemplate = $itemTemplateElements->item(0);
                        // Remove the original template item from the DOM, we'll append clones
                        $itemTemplate->parentNode->removeChild($itemTemplate);

                        $renderedItemsFragment = $dom->createDocumentFragment();

                        foreach ($collectionData as $itemData) {
                            /** @var DOMElement $clonedItem */
                            $clonedItem = $itemTemplate->cloneNode(true);
                            $clonedItem->removeAttribute('data-repeat-item');

                            // Process placeholders within the cloned item's attributes and text content
                            $this->processPlaceholdersInNode($clonedItem, $itemVarName, $itemData);

                            $renderedItemsFragment->appendChild($clonedItem);
                        }
                        // Replace the original data-repeat element with the fragment of rendered items
                        $repeatElement->parentNode->replaceChild($renderedItemsFragment, $repeatElement);
                    } else {
                        // If no data-repeat-item found, just remove the data-repeat attribute
                        $repeatElement->removeAttribute('data-repeat');
                    }
                } else {
                    // Collection data not found or not iterable, remove the data-repeat element
                    if ($repeatElement->parentNode) { // Ensure parent exists before attempting to remove
                         $repeatElement->parentNode->removeChild($repeatElement);
                    }
                }
            } else {
                // Malformed data-repeat attribute, remove it
                $repeatElement->removeAttribute('data-repeat');
            }
        }

        // --- Step 2: Convert the DOMDocument back to a string after DOM manipulations ---
        $output = $dom->saveHTML();

        // --- Step 3: Process simple {{variable}} replacements ---
        $output = $this->replaceSimpleVariables($output, $this->variables);

        // --- Step 4: Process {{ functionName(params) }} calls ---
        $output = $this->replaceFunctionCalls($output);

        return $output;
    }

    /**
     * Recursively processes placeholders (e.g., item[key]) within a DOM node and its children.
     * This method is specifically for the new array-like access within data-repeat items.
     */
    protected function processPlaceholdersInNode(DOMElement $node, string $itemVarName, array|object $itemData): void
    {
        // Process attributes
        foreach ($node->attributes as $attr) {
            $originalValue = $attr->value;
            $newValue = $this->replaceDataAttributePlaceholders($originalValue, $itemVarName, $itemData);
            if ($originalValue !== $newValue) {
                $attr->value = $newValue;
            }
        }

        // Process text content of the node itself if it's a text node directly inside
        if ($node->nodeType === XML_ELEMENT_NODE && $node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                if ($childNode->nodeType === XML_TEXT_NODE) {
                    $originalValue = $childNode->nodeValue;
                    $newValue = $this->replaceDataAttributePlaceholders($originalValue, $itemVarName, $itemData);
                    if ($originalValue !== $newValue) {
                        $childNode->nodeValue = $newValue;
                    }
                }
            }
        }

        // Recursively process children
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $this->processPlaceholdersInNode($child, $itemVarName, $itemData);
            }
        }
    }

    /**
     * Replaces placeholders like 'item[key]' in a string.
     */
    protected function replaceDataAttributePlaceholders(string $content, string $itemVarName, array|object $itemData): string
    {
        // Regex to match 'item[key]' pattern
        $pattern = '/' . preg_quote($itemVarName) . '\[([a-zA-Z0-9_]+)\]/';

        return preg_replace_callback($pattern, function($matches) use ($itemData) {
            $key = $matches[1];
            if (is_array($itemData) && isset($itemData[$key])) {
                return (string)$itemData[$key];
            } elseif (is_object($itemData) && property_exists($itemData, $key)) {
                return (string)$itemData->$key;
            }
            return $matches[0]; // Return original if not found
        }, $content);
    }


    /**
     * Replaces simple {{key}} variables in the content.
     * This is separate from the item[key] processing.
     */
    protected function replaceSimpleVariables(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            if (is_string($value) || is_numeric($value) || is_bool($value) || is_null($value)) {
                $content = str_replace('{{' . $key . '}}', (string)$value, $content);
            }
            // Note: This method is specifically for simple {{key}} variables.
            // Dot notation for global variables would need to be added here if desired.
        }
        return $content;
    }

    /**
     * Processes and replaces custom function calls like {{ myFunc(param1, 'param2') }} in the content.
     *
     * @param string $content The template content.
     * @return string The content with function calls replaced.
     */
    protected function replaceFunctionCalls(string $content): string
    {
        // Regex to find {{ functionName(params) }}
        // Captures: 1=functionName, 2=params string
        $pattern = '/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\((.*?)\)\s*\}\}/';

        return preg_replace_callback($pattern, function ($matches) {
            $functionName = $matches[1];
            $paramsString = $matches[2]; // e.g., "89, 'hello', someVar"

            if (isset($this->functions[$functionName]) && is_callable($this->functions[$functionName])) {
                $callback = $this->functions[$functionName];

                // Parse parameters string into an array of actual values
                $args = [];
                if (!empty($paramsString)) {
                    // Simple parsing: split by comma, trim, and attempt to cast to int/float/bool/null
                    // This is a basic parser. For complex types (arrays, objects), you'd need a more robust parser.
                    $rawArgs = explode(',', $paramsString);
                    foreach ($rawArgs as $arg) {
                        $arg = trim($arg);
                        if (is_numeric($arg)) {
                            $args[] = str_contains($arg, '.') ? (float)$arg : (int)$arg;
                        } elseif (in_array(strtolower($arg), ['true', 'false'])) {
                            $args[] = (strtolower($arg) === 'true');
                        } elseif (strtolower($arg) === 'null') {
                            $args[] = null;
                        } elseif (str_starts_with($arg, "'") && str_ends_with($arg, "'")) {
                            $args[] = trim($arg, "'"); // String literal with single quotes
                        } elseif (str_starts_with($arg, '"') && str_ends_with($arg, '"')) {
                            $args[] = trim($arg, '"'); // String literal with double quotes
                        } elseif (isset($this->variables[$arg])) {
                            // If it's a variable name, use its value from $this->variables
                            $args[] = $this->variables[$arg];
                        } else {
                            // Treat as a string if no other type matches, or handle as an error
                            $args[] = $arg;
                        }
                    }
                }

                try {
                    // Call the defined function with the parsed arguments
                    $result = call_user_func_array($callback, $args);
                    return (string)$result; // Return the result as a string
                } catch (\Exception $e) {
                    // Handle errors during function execution (e.g., log, return empty string)
                    error_log("Template function '{$functionName}' failed: " . $e->getMessage());
                    return ''; // Or return an error message placeholder
                }
            }
            return $matches[0]; // If function not found, return the original placeholder
        }, $content);
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

            $contentToLoad = $this->templateContent; // Use the raw content for DOM parsing

            // Check if the template content already has a full HTML structure
            $hasFullHtmlStructure = (
                str_starts_with(trim($this->templateContent), '<!DOCTYPE') ||
                str_starts_with(trim($this->templateContent), '<html')
            );

            // If the content is a fragment, wrap it in a full HTML structure
            if (!$hasFullHtmlStructure) {
                // Wrapping the fragment in a full HTML structure is crucial for DOMDocument
                // to parse it reliably without moving elements around.
                $contentToLoad = '<!DOCTYPE html><html><head></head><body>' . $this->templateContent . '</body></html>';
            } else {
                // If it already has a full structure, LIBXML_HTML_NOIMPLIED can be useful
                // to prevent DOMDocument from adding implied body/html if the template is
                // already well-formed but starts within the <html> tag for example.
                $options |= LIBXML_HTML_NOIMPLIED;
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
        // This method becomes less relevant with the new DOM-based approach,
        // but can check for simple {{key}} variables.
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
        // This will now render the DOMDocument's current state.
        return $this->render(); // Call render to ensure full processing
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