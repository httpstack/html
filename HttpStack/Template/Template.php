<?php
namespace HttpStack\Template;

class Template extends DOMDocument{
    protected DOMXPath $map;
    protected array $vars = [];
    public function __construct(string $baseTemplate){
        
    }
}
?>