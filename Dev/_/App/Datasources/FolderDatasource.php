<?php
namespace App\Datasources;

use App\Datasources\Contracts\CRUD;

class FolderDatasource implements CRUD
{
    protected array $data = [];

    public function __construct(array $config=[])
    {
        // Initialize with initial data if provided
        $this->data = ["key1" => "value1", "key2" => "value2"];
    }
    public function read(array $query = []): array
    {
        // Simulate reading data from a data source
        return $this->data;
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