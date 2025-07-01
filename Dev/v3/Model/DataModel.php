<?php
namespace Dev\v3\Model;
use Dev\Interfaces\CRUD;
use Dev\v3\Model\AbstractDataModel;
class DataModel extends AbstractDataModel {
    public function __construct(protected CRUD $datasource) {
        //The class implementing DatasourceInterface must be passed to the constructor
        parent::__construct($datasource);
        //This will call the read method on the datasource to populate the model with data
        $this->read();
    }

    public function read(): array {
        $data = $this->datasource->read();
        $this->setAll($data);
        return $data;
    }
}
?>