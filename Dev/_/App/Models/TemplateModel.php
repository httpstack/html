<?php
namespace App\Models;

use Stack\Model\AbstractModel;
use App\Datasources\Contracts\CRUD;
use Stack\Datasource\AbstractDatasource;

class TemplateModel extends AbstractModel  
{
    /**
     * Constructor for the Model class.
     *
     * @param \Dev\v3\Interfaces\CRUD $datasource The datasource for this model.
     * @param array $initialData Initial data to set in the model.
     */
    public function __construct(AbstractDatasource $datasource)
    {
        parent::__construct($datasource);
    }
}
?>