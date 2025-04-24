<?php
namespace Base\App;
use \DOMElement;
use \DOMXPath;
use \DOMDocument;

class _Template extends DOMDocument
{
    private DOMXPath $objDocumentMAP;



    public function __construct
    (
        private string $strBaseTemplate,
        private array $arrAssets = [["css" => []], ["js" => []]],
        private array $arrKeyVals = [],
    )
    {
        parent::__construct('1.0', 'UTF-8');
        $this->preserveWhiteSpace = false;
        $this->formatOutput = true;

        $this->loadHTMLFile($this->strBaseTemplate, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NOBLANKS);
        $this->objDocumentMAP = new DOMXPath($this); 
        $this->loadAssets();
    }
    private function loadAssets(): void
    {

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