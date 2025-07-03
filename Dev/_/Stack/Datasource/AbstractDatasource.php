<?php
namespace App\Datasources;
use Stack\Datasource\Contracts\CRUD;
use Stack\Datasource\Contracts\Datasource;
abstract class AbstractDatasource implements CRUD, Datasource
{
    protected array $dataCache = [];
    protected bool $readOnly = true;
    protected mixed $endPoint = null;
    public function __construct(array $config)
    {
        if(!$config){
            throw new \InvalidArgumentException("Configuration array is required.");
        }
        $this->crudHandlers = $config['crudHandlers'];
        $this->readOnly     = $config['readOnly'];
        $this->endPoint     = $config['endPoint'];
    }
    /**
     * Implement the Datasource Contract
     *
     * @method setReadOnly
     * @method isReadOnly
     * @method disconnect
     * @param bool $readOnly
     */
    public function setReadOnly(bool $readOnly): void
    {
        $this->readOnly = $readOnly;
    }
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }
    public function disconnect(): mixed
    {
        // Simulate disconnecting from a data source
        return true;
    }


    /**
     * Implement the CRUD Contract
     *
     * @method create
     * @method read
     * @method update
     * @method delete
     * @param array $data
     */
    public function read(array $keys = []): array
    {
        if (empty($this->crudHandlers['read'])) {
            throw new \RuntimeException("Read handler is not defined.");
        }
        if($this->dataCache){
            return array_intersect_key($this->dataCache, array_flip($keys));
        }
        $this->dataCache = call_user_func($this->crudHandlers['read'], $keys, $this->endPoint);
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