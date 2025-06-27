<?php 
namespace Dev\v2_0;
//use Dev\v2_0\
//use Dev\v2_0\
//use Dev\v2_0\
use Dev\v2_0\CrudInterface;
use Dev\v2_0\AbstractDataModel;
class TemplateDataModel extends AbstractDataModel implements CrudInterface{
    public function __construct(protected DatasourceInterface&CrudInterface $datasource){
        $dataSet = $this->datasource->read();
        parent::__construct($dataSet);
    }
}
?>