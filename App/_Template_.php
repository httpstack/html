<?php
namespace App;
use App\Container;
use \DOMElement;
use \DOMXPath;
use \DOMDocument;
use \DOMNode;
use \DOMDocumentFragment;
use \DOMException;
use \DOMNodeList;
use \DOMText;
use \DOMAttr;
use \DOMNamedNodeMap;

class _Template extends DOMDocument
{
    private DOMXPath $objDocumentMAP;
    private string $basePath;
    private string $layoutPath;
    
 

    /**
     * The document before the handlebar expressions are replaced
     * @var string
     */
    public string $htmDoc= '';

    public function render(string $strTemplate, array $arrData = []): string
    {
        parent::__construct('1.0', 'UTF-8');
        $this->loadHTMLFile($strTemplate);
        $this->objDocumentMAP = new DOMXPath($this);

        //var_dump($this->htmDoc);
        //var_dump($arrData);
        return $this->saveHTML();
    }

    //loadBaseTemplate
    private function loadAssets($assetUri) : void
    {
        $arrAssets = $this->container->settings['Template']['assets'];
        //var_dump($arrAssets);
    
        foreach ($arrAssets as $dir => $files) {

            
            foreach($files as $file){
                switch($dir){
                    case 'css/':
                        $this->insertHTML($this->getByTag('head'), "<link rel='stylesheet' href='$assetUri$dir$file' />");
                        break;
                    case 'js/':
                        $this->insertHTML($this->getByTag('body'), "<script src='$assetUri$dir$file'></script>");
                        break;
                    /*
                    case 'img/':
                        $this->insertHTML($this->getByTag('body'), "<img src='$assetPath$dir$file' />");
                        break;
                    case 'fonts/':
                        $this->insertHTML($this->getByTag('head'), "<link rel='stylesheet' href='$assetPath$dir$file' />");
                        break;
                    */
                }
            }
        }
    }
    public function is_html(string $strHTML): bool
    {
        return (bool) preg_match('/<[^<]+>/', $strHTML);
    }
    public function getByClass(string $strClass): ?DOMNode
    {
        return $this->getNode("//*[contains(concat(' ', normalize-space(@class), ' '), ' $strClass ')]");
    }
    public function getByName(string $strName): ?DOMNode
    {
        return $this->getNode("//*[@name='$strName']");
    }
    public function getByAttribute(string $strAttribute): ?DOMNode
    {
        return $this->getNode("//*[@$strAttribute]");
    }
    public function arrToUl(array $arr, $raw, $cfg=[]): string|DOMDocumentFragment
    {
        //$cfg will have a class list for each element 
        // used inthe navbar
        if (empty($cfg)) {
            $cfg = ["ul" => "nav-ul", "li" => "nav-li", "a" => "nav-a"];
        }
        
        $strHTML = "<ul class='{$cfg['ul']}'>";
        foreach ($arr as $key => $value) {
            $href = $value['url'] ?? '#';
            $label = $value['label'] ?? $key;
            $active = $value['active'] ?? false;
            if ($active) {
                $cfg['li'] .= " active";
            }
            if (isset($value['icon'])) {
                $label = "<i class='{$value['icon']}'></i> $label";
            }
            $strHTML .= "<li class='{$cfg['li']}'><a class='{$cfg['a']}' href='$href'>$label</a></li>";
        }
        $strHTML .= "</ul>";
        if ($raw) {
            return $strHTML;    
        }
        $fragment = $this->createDocumentFragment();
        $fragment->appendXML($strHTML);
        return $fragment;    
    }

    public function getByAttributeValue(string $strAttribute, string $strValue): ?DOMNode
    {
        return $this->getNode("//*[@$strAttribute='$strValue']");
    }
    public function getByAttributeContains(string $strAttribute, string $strValue): ?DOMNode
    {
        return $this->getNode("//*[contains(concat(' ', normalize-space(@$strAttribute), ' '), ' $strValue ')]");
    }
    public function getByIDContains(string $strID): ?DOMNode
    {
        return $this->getNode("//*[contains(concat(' ', normalize-space(@id), ' '), ' $strID ')]");
    }
    public function getByIDContainsValue(string $strID, string $strValue): ?DOMNode
    {
        return $this->getNode("//*[@id='$strID' and contains(concat(' ', normalize-space(@id), ' '), ' $strValue ')]");
    }
    public function getByIDContainsClass(string $strID, string $strClass): ?DOMNode
    {
        return $this->getNode("//*[@id='$strID' and contains(concat(' ', normalize-space(@class), ' '), ' $strClass ')]");
    }
    public function getByIDClass(string $strID, string $strClass): ?DOMNode
    {
        return $this->getNode("//*[@id='$strID' and contains(concat(' ', normalize-space(@class), ' '), ' $strClass ')]");
    }

    public function getByID(string $id): ?DOMNode
    {
        return $this->getNode("//*[@id='$id']");
    }

    public function getByTag(string $strTag): ?DOMNode
    {
        return $this->getNode("//$strTag");
    }

    public function getNode(string $strXPath): ?DOMNode
    {
        return $this->objDocumentMAP->query($strXPath)->item(0);
    }

    public function insertText(DOMNode $node, string $strText): void
    {
        $node->textContent = $strText;
    }

    public function insertHTML(DOMNode $node, string $strHTML): void
    {
        $objFragment = $this->createFragment($strHTML);
        $node->appendChild($objFragment);
    }

    public function createFragment(string $strHTML): DOMDocumentFragment
    {
        $objFragment = $this->createDocumentFragment();
        $objFragment->appendXML($strHTML);
        return $objFragment;
    }
}  