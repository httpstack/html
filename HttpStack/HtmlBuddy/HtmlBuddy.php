<?php

class HtmlBuddy
{
    // --- Constants for search types
    const HTBUDDY_BYID = 1;
    const HTBUDDY_BYCLASS = 2;
    const HTBUDDY_BYTAG = 3;
    const HTBUDDY_BYXPATH = 4;
    const HTBUDDY_BYCSS = 5;

    // --- Default delimiters for templating
    const HTBUDDY_MUSTACHE = ['{{', '}}'];
    const HTBUDDY_HBAR = ['{%', '%}'];

    // --- Internals
    protected DOMDocument $dom;
    protected DOMXPath $xpath;
    protected array $data = [];
    protected array $functions = [];

    public function __construct(string $html = '')
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        if ($html) {
            $this->dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        }
        $this->xpath = new DOMXPath($this->dom);
        libxml_clear_errors();
    }

    // --- Load a file
    public function loadFile(string $file): self
    {
        $html = file_get_contents($file);
        $this->__construct($html);
        return $this;
    }

    // --- Save
    public function save(?DOMNode $context = null, bool $raw = false): string|DOMDocument
    {
        $html = $context ? $this->dom->saveHTML($context) : $this->dom->saveHTML();
        return $raw ? $html : $this->dom;
    }

    // --- Get method
    public function get(string $expr, int $type = self::HTBUDDY_BYCSS, bool $raw = false): mixed
    {
        $query = '';

        switch ($type) {
            case self::HTBUDDY_BYID:
                $query = "//*[@id='$expr']";
                break;
            case self::HTBUDDY_BYCLASS:
                $query = "//*[contains(concat(' ', normalize-space(@class), ' '), ' $expr ')]";
                break;
            case self::HTBUDDY_BYTAG:
                $query = "//$expr";
                break;
            case self::HTBUDDY_BYXPATH:
                $query = $expr;
                break;
            case self::HTBUDDY_BYCSS:
            default:
                $query = $this->cssToXpath($expr);
                break;
        }

        $nodes = $this->xpath->query($query);
        if (!$nodes) return null;

        if ($nodes->length == 1) {
            return $raw ? $this->dom->saveHTML($nodes->item(0)) : $nodes->item(0);
        }
        return $nodes;
    }

    // --- Set (append)
    public function set(DOMNode $parent, DOMNode $child): self
    {
        $parent->appendChild($child);
        return $this;
    }

    // --- Create new element
    public function createElement(string $tag, array $attributes = [], ?string $content = null): DOMElement
    {
        $element = $this->dom->createElement($tag);
        foreach ($attributes as $k => $v) {
            $element->setAttribute($k, $v);
        }
        if ($content) {
            $element->appendChild($this->dom->createTextNode($content));
        }
        return $element;
    }

    // --- Create fragment
    public function createFragment(string $html): DOMDocumentFragment
    {
        $frag = $this->dom->createDocumentFragment();
        @$frag->appendXML($html);
        return $frag;
    }

    // --- Insert before
    public function insertBefore(DOMNode $refNode, DOMNode $newNode): self
    {
        $refNode->parentNode->insertBefore($newNode, $refNode);
        return $this;
    }

    // --- Replace node
    public function replace(DOMNode $oldNode, DOMNode $newNode): self
    {
        $oldNode->parentNode->replaceChild($newNode, $oldNode);
        return $this;
    }

    // --- Inject file
    public function inject(string $file, DOMNode $target): self
    {
        $html = file_get_contents($file);
        $frag = $this->createFragment($html);
        $target->appendChild($frag);
        return $this;
    }

    // --- Expression Replace
    public function replaceExpressions(array|string $delimiters, array $data, DOMNode $context = null): self
    {
        if (is_string($delimiters)) {
            $delimiters = match ($delimiters) {
                'MUSTACHE' => self::HTBUDDY_MUSTACHE,
                'HBAR' => self::HTBUDDY_HBAR,
                default => ['{{', '}}']
            };
        }
    
        [$start, $end] = $delimiters;
        $html = $this->save($context, true);
    
        // --- First pass: Replace data
        foreach ($data as $key => $value) {
            $html = str_replace("$start$key$end", $value, $html);
        }
    
        // --- Second pass: Execute functions if matching
        $pattern = '/' . preg_quote($start) . '([\w\-]+)(\((.*?)\))?' . preg_quote($end) . '/';

        $html = preg_replace_callback($pattern, function ($matches) {
            $funcName = $matches[1];
            $paramString = $matches[3] ?? '';
        
            if (isset($this->functions[$funcName])) {
                $params = [];
        
                if (!empty($paramString)) {
                    // Split parameters by comma and trim whitespace
                    $params = array_map('trim', explode(',', $paramString));
                    
                    // Optional: Remove quotes if needed
                    $params = array_map(function($param) {
                        return trim($param, '\'"');
                    }, $params);
                }
        
                return call_user_func_array($this->functions[$funcName], $params);
            }
        
            return '';
        }, $html);
        
    
        // --- Reload new HTML
        $newDom = new DOMDocument();
        libxml_use_internal_errors(true);
        $newDom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
    
        $this->dom = $newDom;
        $this->xpath = new DOMXPath($this->dom);
    
        return $this;
    }
    

    // --- Traverse with callback
    public function traverse(callable $callback, ?DOMNode $context = null): self
    {
        $context = $context ?? $this->dom->documentElement;
        $this->recursiveTraverse($callback, $context);
        return $this;
    }

    protected function recursiveTraverse(callable $callback, DOMNode $node)
    {
        $callback($node);
        foreach ($node->childNodes as $child) {
            $this->recursiveTraverse($callback, $child);
        }
    }

    // --- Convert array to tags
    public function arrayToTag(array $arr, string $tag, ?string $parentTag = null): DOMNode
    {
        $frag = $this->dom->createDocumentFragment();

        foreach ($arr as $item) {
            $el = $this->createElement($tag, [], $item);
            if ($parentTag) {
                $wrapper = $this->createElement($parentTag);
                $wrapper->appendChild($el);
                $frag->appendChild($wrapper);
            } else {
                $frag->appendChild($el);
            }
        }

        return $frag;
    }

    // --- Array to NAV
    public function arrayToNav(array $navItems, ?array $classes = null): DOMElement
    {
        $cfg = $classes ?? ["ul" => "nav-ul", "li" => "nav-li", "a" => "nav-a"];
        $ul = $this->createElement('ul', ['class' => $cfg['ul']]);

        foreach ($navItems as $label => $info) {
            $li = $this->createElement('li', ['class' => $cfg['li']]);
            $a = $this->createElement('a', [
                'class' => $cfg['a'],
                'href' => $info['uri']
            ], ($info['icon'] ?? '') . ' ' . $label);

            $li->appendChild($a);
            $ul->appendChild($li);
        }

        return $ul;
    }

    // --- Data container functions
    public function addData(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function removeData(string $key): self
    {
        unset($this->data[$key]);
        return $this;
    }

    public function getData(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    // --- Functions for template expressions
    public function addFunction(string $name, callable $fn): self
    {
        $this->functions[$name] = $fn;
        return $this;
    }

    public function callFunction(string $name, ...$args): mixed
    {
        return $this->functions[$name](...$args);
    }

    // --- Render method
    public function render(string|DOMNode $source, array $data, bool $raw = false): string|DOMDocument
    {
        if (is_string($source)) {
            $this->dom = new DOMDocument('1.0', 'UTF-8');
            libxml_use_internal_errors(true);
            $this->dom->loadHTML($source, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
            $this->xpath = new DOMXPath($this->dom);
        } else {
            $this->dom = $source->ownerDocument;
            $this->xpath = new DOMXPath($this->dom);
        }
    
        $this->replaceExpressions(self::HTBUDDY_MUSTACHE, $data);
    
        return $this->save(null, $raw);
    }

    // --- CSS to XPath (basic)
    public function cssToXpath(string $css): string
    {
        $css = trim($css);
        $css = str_replace(' ', '//', $css);
        $css = preg_replace('/#([\w\-]+)/', "*[@id='$1']", $css);
        $css = preg_replace('/\.([\w\-]+)/', "[contains(concat(' ', normalize-space(@class), ' '), ' $1 ')]", $css);
        $css = '//' . $css;
        return $css;
    }

    // --- Simple XPath to CSS (basic)
    public function xpathToCss(string $xpath): string
    {
        // NOTE: This will be rough/basic; real conversion is complex
        $css = str_replace('//', ' ', $xpath);
        $css = preg_replace('/\*\[@id=\'([^\']+)\'\]/', '#$1', $css);
        $css = preg_replace('/\[contains\(concat\(\' \', normalize-space\(@class\), \' \'\), \' ([^\']+) \'\\)\]/', '.$1', $css);
        return trim($css);
    }

    // --- Check if string is HTML
    public function isHtml(string $string): bool
    {
        return $string !== strip_tags($string);
    }
}



$buddy = new HtmlBuddy('<p>Hello, {{name}}! Today is {{getDate}}.</p>');

$buddy->addData('name', 'John');
$buddy->addFunction('getDate', function () {
    return date('l, F j');
});

echo $buddy->render($buddy->save(), [], true);

// Output:
// <p>Hello, John! Today is Saturday, April 26.</p>
