<?php
namespace Stack\Datasource;

use Closure;
use Stack\Datasource\Contracts\CRUD;
use Stack\Datasource\Contracts\Datasource;
abstract class AbstractDatasource implements CRUD, Datasource
{
    /**
     * @var array $dataCache Cache for data read from the data source
     */
    protected array $dataCache = [];
    /**
     * @var bool $readOnly Indicates if the datasource is read-only
     */
    protected bool $readOnly = true;
    /**
     * @var mixed $endPoint Endpoint for the data source, can be a URL or a database connection string
     */
    protected mixed $endPoint = null;
    /**
     * @var array $crudHandlers Handlers for CRUD operations
     */
    protected array $crudHandlers = [];

    /**
     * @var callable|null $formatFilter Optional filter function to format the data
     */
    protected ?Closure $formatFilter = null;
    /**
     * Constructor to initialize the datasource with configuration
     *
     * @param array $config Configuration array containing 'crudHandlers', 'readOnly', and 'endPoint'
     * @throws \InvalidArgumentException if the configuration is not provided or invalid
     */
    public function __construct(array $config)
    {
        if(!$config){
            throw new \InvalidArgumentException("Configuration array is required.");
        }
        $this->crudHandlers = $config['crudHandlers'];
        $this->readOnly     = $config['readOnly'];
        $this->endPoint     = $config['endPoint'];
        $this->formatFilter = $config['formatFilter'] ?? null;
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
        $this->dataCache = call_user_func($this->crudHandlers['read'], $this->endPoint, $keys, $this->formatFilter);
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