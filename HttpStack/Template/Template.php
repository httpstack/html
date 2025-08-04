<?php

namespace HttpStack\Template;

use \DOMXPath;
use \DOMElement;
use \DOMDocument;
use \DOMNodeList;
use HttpStack\IO\FileLoader;
use HttpStack\Container\Container;
use HttpStack\App\Models\TemplateModel;
use HttpStack\Model\Concrete\BaseModel;

class Template
{
    protected DOMDocument $dom;
    protected DOMXPath $xpath;
    protected array $templates = [];
    protected array $assets = [];
    protected array $functions = [];
    protected array $data = [];
    protected Container $container;

    public function __construct(protected ?Container $c)
    {
        if ($c) {
            $this->container = $c;
            $tm = $c->make(TemplateModel::class);
            $fl = $c->make(FileLoader::class);
            $this->data = $tm->getAll();
            $baseLayout = $tm->get('baseLayout');
            $this->registerTemplate("base", $baseLayout);
            $this->dom = new DOMDocument();
            $this->dom->preserveWhiteSpace = false;
            $this->dom->formatOutput = true;
            $this->xpath = new DOMXPath($this->dom);
            $this->initTemplate("base");
            $this->assets = $tm->get("assets");
        } else {
        }
    }
    public function appendAssets(array $assets): void
    {
        foreach ($assets as $asset) {
        }
    }
    public function applyModel(BaseModel $model): void
    {
        $this->data = $model->getAll();
    }

    public function registerTemplate(string $namespace, string $fileName): void
    {
        echo "Registering template: $namespace from file: $fileName\n";
        $fl = $this->container->make(FileLoader::class);

        $path = $fl->findFile($fileName, null, "html");
        $fileContent = $fl->readFile($path);
        $this->templates[$namespace] = $fileContent;
    }
    public function initTemplate(string $templateName): void
    {
        if (!isset($this->templates[$templateName])) {
            throw new \Exception("Template not found: " . $templateName);
        }
        @$this->dom->loadHTML($this->templates[$templateName], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING | LIBXML_NOERROR);
        $this->xpath = new DOMXPath($this->dom);
    }
    public function getDOM(): DOMDocument
    {
        return $this->dom;
    }
    protected function dom(?string $htmlOrFile)
    {
        if (file_exists($htmlOrFile)) {
            $this->dom->loadHTMLFile($htmlOrFile, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING | LIBXML_NOERROR);
        } else if (is_html($htmlOrFile)) {
            $this->dom->loadHTML($htmlOrFile, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING | LIBXML_NOERROR);
        }
        return $this->dom;
    }
    protected function xpath(?DOMDocument $dom): DOMXPath
    {
        if ($dom) {
            $this->xpath = new DOMXPath($dom);
        }
        return $this->xpath;
    }
    public function render()
    {
        return $this->dom->saveHTML();
    }

    public function getXPath(): DOMXPath
    {
        return $this->xpath;
    }
}
