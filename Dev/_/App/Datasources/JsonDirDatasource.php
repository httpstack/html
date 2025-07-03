<?php
namespace App\Datasources;

use Stack\Datasource\AbstractDatasource;

class DirDatasource extends AbstractDatasource
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }
    public function read(array $query = []): array
    {  
        $this->dataCache = parent::read($this->endPoint, $query);
        
        // Simulate reading data from a data source
        return $this->dataCache;
    }
    public function create(array $payload):mixed{
        return true;
    }
    public function update(array $payload):mixed{
        return true;
    }
    public function delete(array $payload):mixed{
        // Simulate deleting data from a data source
        return true;
    }
}
?>