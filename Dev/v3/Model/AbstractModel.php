<?php
namespace Dev\v3\Model;

use Dev\v3\BaseModel;
use Dev\v3\Interfaces\AttributeState;

abstract class AbstractModel extends BaseModel implements AttributeState{

    /**
     * @var array $states - This is the only thing you need to create
     * @var array $attributes - This array is created by the abstract class extending the
     * attribute implementation model
     */
    protected array $states = [];
    /**
     * @var array $attributes - This array is inherited from the BaseModel class
     * which implements the Attributes interface
     */
    public function __construct(protected CrudInterface $datasource){
        parent::__construct();
        // Initialize the states array if needed
        $this->states = [];
    }
    // Implement methods from AttributeState interface
    //The current state of the data model is $this->attributes
    // which is inherited from the basemodel class implemtnaion
    // of the attributes interface
    //The states are stored in $this->states
    public function pushState(string $restorePoint){
        $this->states[$restorePoint] = $this->attributes;
    }
    public function popState(){
        $lastKey = array_key_last($this->states);
        if ($lastKey !== null) {
            $lastState = $this->states[$lastKey];
            unset($this->states[$lastKey]);
            $this->attributes = $lastState; // Restore the last state
            return $lastState;
        }
        return []; // Return an empty array if no states are available
    }
    public function getState(string $restorePoint){
        return $this->states[$restorePoint] ?? null; // Return null if the state does not exist
    }
    // Additional methods can be added here as needed

    //Now you have to redefine the set, setAll, remove, and clear methods on 
    // attributes class because any methods that will augmen
    // // mutate the model, u have to first push the state of the model onto the 
    // the states array
    public function set(string $key, mixed $value): void {
        $this->pushState('before_set_' . $key);
        parent::set($key, $value);
    }
    public function setAll(array $data): void {
        foreach ($data as $key => $value) {
            $this->pushState('before_set_' . $key);
        }
        parent::setAll($data);
    }
    public function remove(string $key): void {
        $this->pushState('before_remove_' . $key);
        parent::remove($key);
    }
}
?>