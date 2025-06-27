<?php 
namespace Dev\v2_0;
use Dev\v2_0\IF_Atrributes;
use Dev\v2_0\IF_AtrributeState;
use Dev\v2_0\BaseModel;
use Dev\v2_0\DatasourceInterface;
//use Dev\v2_0\
//use Dev\v2_0\
abstract class AbstractModel extends BaseModel implements AtrributeStateInterface {
    protected array $states = [];
    public function __construct(protected DatasourceInterface&CrudInterface $datasource) {
        parent::__construct($this->datasource->read());
        // Initialization code if needed
    }

// ...existing code...
protected function pushState(): string {
    $key = uniqid('_state_', true) . '_' . count($this->states);
    $this->states[$key] = $this->_attributes;
    return $key;
}

public function popState(string $key): array {
    if (array_key_exists($key, $this->states)) {
        $value = $this->states[$key];
        unset($this->states[$key]);
        return $value;
    }
    throw new \Exception("State with key {$key} does not exist.");
}

public function getState(mixed $restorePoint): array {
    if (empty($this->states)) {
        throw new \Exception("No states available.");
    }
    if (!array_key_exists($restorePoint, $this->states)) {
        throw new \Exception("State with key {$restorePoint} does not exist.");
    }
    return $this->states[$restorePoint];
}
    /**
     * Redefine the methods that modify the data model to 
     * save a snapshot to the states array
     *
     * @method set
     * @method setAll
     * @method remove
     * @method clear
     */
    public function set(mixed $key, mixed $value): void {
        $this->pushState();
        parent::set($key, $value);
    }

    public function setAll(array $attributes): void {
        $this->pushState();
        parent::setAll($attributes);
    }

    public function remove(mixed $key): void {
        $this->pushState();
        parent::remove($key);
    }

    public function clear(): void {
        $this->pushState();
        parent::clear();
    }

}
// End of Dev/v2_0/Abs_Model.php
?>