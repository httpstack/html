<?php
namespace Base\App;
use \DOMElement;
use \DOMXPath;
use \DOMDocument;

class Template extends DOMDocument
{
    private DOMXPath $objDocumentMAP;
    private array $arrAppData = [];
    private string $strRenderedHTML = '';

    public function __construct(string $strURI, array $arrConfig)
    {
        parent::__construct('1.0', 'UTF-8');
        $this->preserveWhiteSpace = false;
        $this->formatOutput = true;

        // Load the base template
        $strTemplatePath = $arrConfig['templatePath'];
        $this->loadHTMLFile($strTemplatePath, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NOBLANKS);
        $this->objDocumentMAP = new DOMXPath($this);

        // Store app data
        $this->arrAppData = $arrConfig;

        // Build the base template
        $this->renderBase($strURI);
    }

    public function renderBase(string $strURI): void
    {
        // Insert app name
        $this->insertText($this->getNode('//*[@data-template="appName"]'), $this->arrAppData['appName']);

        // Insert app logo
        $this->insertText($this->getNode('//*[@data-template="appLogo"]'), $this->arrAppData['appLogo']);

        // Build and insert navigation
        $strNavHTML = $this->buildNav($this->arrAppData['appNav'], 'nav-class', true);
        $this->insertHTML($this->getNode('//*[@data-template="navbar"]'), $strNavHTML);

        // Append assets (CSS and JS)
        $this->appendAssets($this->arrAppData['assets']);
    }

    public function appendAssets(array $arrAssets): void
    {
        foreach ($arrAssets as $type => $files) {
            foreach ($files as $file) {
                if ($type === 'css') {
                    $this->appendCSS($file);
                } elseif ($type === 'js') {
                    $this->appendJS($file);
                }
            }
        }
    }

    private function appendCSS(string $filePath): void
    {
        $head = $this->getByTag('head');
        if ($head) {
            $link = $this->createElement('link');
            $link->setAttribute('rel', 'stylesheet');
            $link->setAttribute('href', $filePath);
            $head->appendChild($link);
        }
    }

    private function appendJS(string $filePath): void
    {
        $body = $this->getByTag('body');
        if ($body) {
            $script = $this->createElement('script');
            $script->setAttribute('src', $filePath);
            $body->appendChild($script);
        }
    }

    public function render(): string
    {
        // Replace {{expressions}} in the template with app data
        $strHTML = $this->saveHTML();
        foreach ($this->arrAppData['appData'] as $key => $value) {
            $strHTML = str_replace("{{{$key}}}", $value, $strHTML);
        }
        $this->strRenderedHTML = $strHTML;
        return $this->strRenderedHTML;
    }

    public function buildNav(array $arrNav, string $strNavClass, bool $bolRaw = true): string|\DOMNode
    {
        $strNavHTML = "<ul class=\"$strNavClass\">";
        foreach ($arrNav as $name => $url) {
            $strNavHTML .= "<li><a href=\"$url\">$name</a></li>";
        }
        $strNavHTML .= '</ul>';

        if ($bolRaw) {
            return $strNavHTML;
        }

        $objFragment = $this->createFragment($strNavHTML);
        return $objFragment;
    }

    public function insertView(string $viewPath): void
    {
        $strViewHTML = file_get_contents($viewPath);
        $objFragment = $this->createFragment($strViewHTML);
        $this->append($this->getNode('//*[@data-view="view"]'), $objFragment);
    }

    public function getByID(string $id): ?\DOMNode
    {
        return $this->getNode("//*[@id='$id']");
    }

    public function getByTag(string $strTag): ?\DOMNode
    {
        return $this->getNode("//$strTag");
    }

    public function getNode(string $strXPath): ?\DOMNode
    {
        return $this->objDocumentMAP->query($strXPath)->item(0);
    }

    public function insertText(\DOMNode $node, string $strText): void
    {
        $node->textContent = $strText;
    }

    public function insertHTML(\DOMNode $node, string $strHTML): void
    {
        $objFragment = $this->createFragment($strHTML);
        $node->appendChild($objFragment);
    }

    public function createFragment(string $strHTML): \DOMDocumentFragment
    {
        $objFragment = $this->createDocumentFragment();
        $objFragment->appendXML($strHTML);
        return $objFragment;
    }
}

?>