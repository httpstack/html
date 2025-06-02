<?php
namespace HttpStack\App\Models;
use HttpStack\Contracts\DatasourceInterface;
use HttpStack\Models\BaseModel;
use HttpStack\Traits\DBModel;
use HttpStack\DataBase\DBConnect;
class TemplateModel extends BaseModel{
    //use DBModel;
    //protected DBConnect $dbConnect;

    public function __construct(DatasourceInterface $jsonDatasource){
        parent::__construct($jsonDatasource);
        
        //$this->dbConnect = app()->getContainer()->make("dbConnect");
    }

    public function save():void{
        $this->save();
    }
    public function getModel():array{
        return $this->model;
    }

}

?>