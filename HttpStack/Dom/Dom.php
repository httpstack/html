<?php 
namespace HttpStack\Dom;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMDocumentFragment;
use DOMXPath;
class Dom extends DOMDocument{
    protected DOMXPath $xpath;

    public function __construct(string $fileOrText){
        parent::__construct("1.0", "utf-8");
        if(str_contains($fileOrText, "<")){
            @$this->loadHTML($fileOrText, LIBXML_NOBLANKS | LIBXML_HTML_NODEFDTD);
        }else{
            @$this->loadHTMLFile($fileOrText, LIBXML_NOBLANKS | LIBXML_HTML_NODEFDTD);
        }
    }
    public function query(){}
    public function byID(){}
    public function item() {
    
    }
    
}

?>