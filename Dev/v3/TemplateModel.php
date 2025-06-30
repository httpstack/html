<?php
namespace Dev\v3;

use Dev\v3\Model\AbstractModel;


class TemplateModel extends AbstractModel{

    /**
     * The purpose of this class is to merley bring the abstract model into scope
     * i guess i can make some high level functioons that make idk formnatting the data or doingf something
     * with the data that is class specific
     * 
     * first we have to call the read method on the construct su[pplied datasoource
     * ]
     */
    protected string $templateName;
    public function __construct(string $name) {
        parent::__construct();
        $this->templateName = $name;
    }


}



?>