<?php
namespace HttpStack\Template;

use DOMXPath;
use DOMElement;
use DomDocument;
use HttpStack\IO\FileLoader;

class Template extends DOMDocument implements \Stringable
{
    public array $files = [];
    protected array $vars = [];
    protected array $assets = [];
    protected FileLoader $fileLoader;
    public string $defaultFileExt = "html";
    protected DOMXPath $XPath;

    public function __construct()
    {
        parent::__construct("1.0", "utf-8");
        $this->fileLoader = box("fileLoader");
    }

    public function loadFile(string $nameSpace, string $baseFileName): string
    {
        // Read a file contents into the $files at $nameSpace => $html
        $this->setFile($nameSpace, $this->fileLoader->readFile($baseFileName));
        return $this->files[$nameSpace];
    }
    
    public function getAssets(): array
    {
        return $this->assets;
    }   
    
    public function getFile(string $nameSpace): string|array
    {
        return $nameSpace ? $this->files[$nameSpace] : $this->files;
    }
    
    public function setFile(string $nameSpace, string $html): self 
    {
        $this->files[$nameSpace] = $html;
        return $this;
    }
