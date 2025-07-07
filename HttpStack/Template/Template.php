<?php
namespace HttpStack\Template;

use DOMXPath;
use DOMElement;
use DomDocument;
use HttpStack\IO\FileLoader;
use HttpStack\App\Models\TemplateModel;

class Template {
    private DOMDocument $dom;
    private DOMXPath $xpath;
    private FileLoader $fileLoader;
    private array $arrMeta = [];
    private array $arrAssets = [
        'css' => [],
        'js' => [],
        'font' => [],
        'image' => []
    ];
    private array $arrNavs = [];

    public function __construct(string $strBaseFile, FileLoader $fileLoader) {
        $this->fileLoader = $fileLoader;
        $strTemplateContent = $this->fileLoader->loadFile($strBaseFile);
        $this->dom = new DOMDocument();
        // Suppress warnings from invalid HTML
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($strTemplateContent);
        libxml_clear_errors();
        $this->xpath = new DOMXPath($this->dom);
    }

    public function setTitle(string $strTitle): void {
        $titleNode = $this->xpath->query('//title')->item(0);
        if (!$titleNode) {
            $titleNode = $this->dom->createElement('title');
            $this->getHead()->appendChild($titleNode);
        }
        $titleNode->nodeValue = htmlspecialchars($strTitle);
    }

    public function addMeta(string $strName, string $strContent): void {
        $this->arrMeta[$strName] = $strContent;
    }

    public function addAsset(string $strNamespace, string $strFilePath): void {
        $strExtension = pathinfo($strFilePath, PATHINFO_EXTENSION);
        if (in_array($strExtension, ['css', 'js', 'woff', 'woff2', 'ttf', 'otf'])) {
            $this->arrAssets[$strExtension][$strNamespace] = $strFilePath;
        } elseif (in_array($strExtension, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
            $this->arrAssets['image'][$strNamespace] = $strFilePath;
        }
    }

    public function addNav(string $strNavName, array $arrNavData, array $arrClasses = []): void {
        $this->arrNavs[$strNavName] = [
            'data' => $arrNavData,
            'classes' => $arrClasses
        ];
    }

    public function render(array $arrData, string $strMode = 'handlebar'): string {
        $this->processMeta();
        $this->processAssets();
        $this->processNavs();

        $strOutput = $this->dom->saveHTML();

        if ($strMode === 'handlebar') {
            $strOutput = $this->renderHandlebars($strOutput, $arrData);
        } elseif ($strMode === 'datakey') {
            $strOutput = $this->renderDataKey($arrData);
        }

        return $strOutput;
    }

    private function renderHandlebars(string $strHtml, array $arrData): string {
        return preg_replace_callback('/{{\s*(\w+)\s*}}/', function ($matches) use ($arrData) {
            $strKey = $matches[1];
            return isset($arrData[$strKey]) ? $arrData[$strKey] : '';
        }, $strHtml);
    }

    private function renderDataKey(array $arrData): string {
        foreach ($arrData as $strKey => $value) {
            $nodes = $this->xpath->query("//*[@data-key='$strKey']");
            foreach ($nodes as $node) {
                $node->nodeValue = $value;
            }
        }
        return $this->dom->saveHTML();
    }

    private function processMeta(): void {
        foreach ($this->arrMeta as $strName => $strContent) {
            $metaNode = $this->dom->createElement('meta');
            $metaNode->setAttribute('name', $strName);
            $metaNode->setAttribute('content', $strContent);
            $this->getHead()->appendChild($metaNode);
        }
    }

    private function processAssets(): void {
        $head = $this->getHead();
        $body = $this->getBody();

        foreach ($this->arrAssets['css'] as $strUrl) {
            $linkNode = $this->dom->createElement('link');
            $linkNode->setAttribute('rel', 'stylesheet');
            $linkNode->setAttribute('href', $strUrl);
            $head->appendChild($linkNode);
        }

        foreach ($this->arrAssets['js'] as $strUrl) {
            $scriptNode = $this->dom->createElement('script');
            $scriptNode->setAttribute('src', $strUrl);
            $body->appendChild($scriptNode);
        }

        if (!empty($this->arrAssets['image'])) {
            $strJsCode = "const arrImages = [];";
            foreach ($this->arrAssets['image'] as $strUrl) {
                $strJsCode .= "arrImages.push('{$strUrl}');";
            }
            $strJsCode .= "arrImages.forEach(url => { const img = new Image(); img.src = url; });";
            $scriptNode = $this->dom->createElement('script', $strJsCode);
            $body->appendChild($scriptNode);
        }
    }

    private function processNavs(): void {
        foreach ($this->arrNavs as $strNavName => $arrNav) {
            $nodes = $this->xpath->query("//*[@data-nav='$strNavName']");
            foreach ($nodes as $node) {
                $this->generateNav($node, $arrNav['data'], $arrNav['classes']);
            }
        }
    }

    private function generateNav(DOMElement $parentElement, array $arrNavData, array $arrClasses): void {
        $ul = $this->dom->createElement('ul');
        if (isset($arrClasses['ul'])) {
            $ul->setAttribute('class', $arrClasses['ul']);
        }

        foreach ($arrNavData as $arrItem) {
            $li = $this->dom->createElement('li');
            if (isset($arrClasses['li'])) {
                $li->setAttribute('class', $arrClasses['li']);
            }

            $a = $this->dom->createElement('a', htmlspecialchars($arrItem['text']));
            $a->setAttribute('href', $arrItem['url']);
            if (isset($arrClasses['a'])) {
                $a->setAttribute('class', $arrClasses['a']);
            }

            $li->appendChild($a);
            $ul->appendChild($li);
        }

        // Clear the placeholder and append the new nav
        $parentElement->nodeValue = '';
        $parentElement->appendChild($ul);
    }

    private function getHead(): DOMElement {
        return $this->xpath->query('//head')->item(0);
    }

    private function getBody(): DOMElement {
        return $this->xpath->query('//body')->item(0);
    }
}