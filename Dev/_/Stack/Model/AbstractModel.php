<?php
namespace Dev\_\Stack\Model;

use Dev\v3\Interfaces\CRUD;
use Dev\_\Stack\Model\Concrete\BaseModel;
abstract class AbstractModel extends BaseModel
{
    protected array $states = []; // Stack to hold states
    protected CRUD $datasource; // The datasource for this model

    public function __construct(CRUD $datasource, array $initialData = []){
        parent::__construct($initialData);
        $this->datasource = $datasource; // Initialize the datasource
        $this->states = []; // Initialize the states array
        // Optionally, you can initialize the model with the initial data
        // Initialize any additional properties or methods specific to AbstractModel
    }

    public function pushState(string $restorePoint): mixed
    {
        $this->states[$restorePoint] = $this->getAll();
        // Implement logic to push a new state onto the stack
        // This could involve saving the current state of attributes
        // and any other relevant data to a stack or array.
        return "State pushed: " . $restorePoint;
    }

    public function popState(): array
    {
        $lastKey = array_key_last($this->states);
        if ($lastKey !== null) {
            $lastState = $this->states[$lastKey];
            unset($this->states[$lastKey]);
            $this->setAll($lastState); // Restore the last state
            return $lastState;
        }
        return []; // Return an empty array if no states are available
    }

    public function getState(string $restorePoint): ?array
    {
        return $this->states[$restorePoint] ?? null;
        // Implement logic to retrieve a specific state from the stack
        // without modifying the current state.
    }

    //REWRITE THE METHODS THAT MUTATE THE MODEL SO THEY PUSH THE STATE
    public function set(string $strKey, mixed $mixValue): void{
        $this->pushState("before_set_{$strKey}");
        parent::set($strKey, $mixValue);
    }
    public function remove(string $strKey): void{
        $this->pushState("before_remove_{$strKey}");
        parent::remove($strKey);
    }
    public function setAll(array $arrData): void{
        $this->pushState("before_setAll");
        parent::setAll($arrData);
    }
    public function clear(): void {
        $this->pushState("before_clear");
        parent::clear();
    }

}
?>