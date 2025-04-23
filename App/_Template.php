<?php
namespace Base\App;
use \DOMElement;
use \DOMXPath;
use \DOMDocument;

class _Template extends DOMDocument
{
    private DOMXPath $objDocumentMAP;
    private array $arrAppData = [];
    private string $strRenderedHTML = '';
    private string $strTemplatePath = '';
    private string $strAssetPath = '';
    private string $strAppName = '';
    private string $strLogoPath = '';
    private array $arrNavLinks = [];
    private array $arrSocialLinks = [];
    private array $arrFooterLinks = [];
    private string $strBaseURL = '';
    private string $strFooterCopy = '';

    public function __construct(private object $container, string $uri)
    {
        $this->container = $container;
        $arrConfig = $this->container->make('config', 'template');
        $this->strTemplatePath = $arrConfig['templatePath'];
        $this->strAssetPath = $arrConfig['assetPath'];
        $this->strAppName = $arrConfig['appName'];
        $this->strLogoPath = $arrConfig['logoPath'];
        $this->arrNavLinks = $arrConfig['navLinks'];
        $this->arrSocialLinks = $arrConfig['socialLinks'];
        $this->arrFooterLinks = $arrConfig['footerLinks'];
        $this->strFooterCopy = $arrConfig['footerCopy'];


        parent::__construct('1.0', 'UTF-8');
        $this->preserveWhiteSpace = false;
        $this->formatOutput = true;

        // Load the base template
        $strTemplatePath = $arrConfig['templatePath'];
        $this->loadHTMLFile($strTemplatePath, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NOBLANKS);
        $this->objDocumentMAP = new DOMXPath($this);

        // Store app data
        $this->arrAppData = $arrConfig;
    }
}