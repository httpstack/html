<?php
namespace Dev\_\App\Models;

use Dev\_\Stack\Model\AbstractModel;
use Dev\v3\Interfaces\CRUD;

class Model extends AbstractModel
{
    /**
     * Constructor for the Model class.
     *
     * @param \Dev\v3\Interfaces\CRUD $datasource The datasource for this model.
     * @param array $initialData Initial data to set in the model.
     */
    public function __construct(CRUD $datasource, array $initialData = [])
    {
        parent::__construct($datasource, $initialData);
        $this->setAll($this->datasource->read());
    }
}
?>