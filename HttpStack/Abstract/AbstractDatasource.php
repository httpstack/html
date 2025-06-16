<?php 
namespace HttpStack\Abstract;
/**
 * DatasourceInterface defines the contract for data sources in the HttpStack framework.
 * It provides methods for fetching and saving data, as well as deleting variables.
 * 
 * @package HttpStack\Contracts
 */
abstract class AbstractDatasource implements DatasourceInterface{
    protected bool $readOnly = false;

    public function __construct(bool $readOnly = true) {
        $this->readOnly = $readOnly;
    }
    public function setReadOnly(bool $readOnly): void {
        $this->readOnly = $readOnly;
    }

    public function isReadOnly(): bool {
        return $this->readOnly;
    }
    /**
     * Fetch data from the datasource.
     * 
     * @param string|array|null $key The key or keys to fetch data for. If null, fetch all data.
     * @return array The fetched data.
     * 
     * Pretty much any kind of lookup is done with fetch
     */
    abstract function fetch(string|array|null $key): array;

    /**
     * Save data to the datasource.
     * 
     * @param array $data The data to save.
     * 
     * All of the operations that create data or update data
     * was thinking of removing a key if you  update it with no value
     * like $model->set('key', null); but thats not intuitive
     * given the name of the method
     */
    abstract function save(array $data): void;

    /**
     * Delete a variable from the model.
     * 
     * @param mixed $var The variable to delete.
     * Remeber that in the case of database datasources,
     * the keys will have values that are records like the key
     * will be prod_id_1234 and the value will be the record,
     * as in: id_23 => ['name' => 'Product Name', 'price' => 100.00]
     * so deketing a key will effectively remove the record+
     * 
     * 
     * 
     * 
     */
    abstract function delete(mixed $var): void;
}
?>