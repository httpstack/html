<?php
namespace HttpStack\Models;
use HttpStack\Contracts\DatasourceInterface;
use HttpStack\DataBase\DBConnect;
use HttpStack\Abstract\AbstractModel;
/**
 * BaseModel is the base class for all models in the HttpStack framework.
 * It provides basic functionality for fetching, saving, and manipulating data.
 * 
 * @package HttpStack\Models
 */
class BaseModel extends AbstractModel{
    
    protected array $model;
    protected array $_model;
    /**
     * The __construct can have optional param for datasources
     * $dataSource will need these key => values
     * type - database|file|folder|object
     * 
     */
    public function __construct(DatasourceInterface $dataSource){
        parent::__construct($dataSource);
        $this->model = [];
        $this->_model = [];
        $this->fetch(null);
    }
    protected function fetch(string|array $key = null):void{
        $data = $this->dataSource->fetch($key);
        $this->set($data);
    }
    protected function save():void{   
            $this->store();
            $this->dataSource->save($this->model);
    }
    public function reStore():array{
        $this->model = $this->_model;
        return $this->model;
    }
    public function store():array{
        $this->_model = $this->model;
        return $this->model;
    }

    /**
     * Remove a value from the model.
     * If the key exists, it will be unset.
     * If the key does not exist, nothing will happen.
     */
    public function remove(string $abstract):void{
        if(isset($this->model[$abstract])){
            unset($this->model[$abstract]);
        }
    }
    /**
     * Clear the model.
     * This will remove all values from the model.
     */
    public function clear():void{
        $this->model = [];
    }
    /**
     * Set a value in the model.
     * If $abstract is an array, it will set multiple values.
     * If $concrete is empty, it will set the value of $abstract to itself.
     */
    public function set(string|array $abstract, string|array $concrete = ''){
        if(is_array($abstract)){
            foreach($abstract as $abstractKey => $concreteValue){
               $this->model[$abstractKey] = $concreteValue; 
            }
            return;
        }
        $this->model[$abstract] = $concrete;
    }
        /**
     * Set multiple values in the model.
     * This will loop over the array and set each key => value pair.
     * If a key already exists, it will be overwritten.
     */
    public function setAll(array $data):void{
        foreach($data as $key => $value){
            $this->set($key, $value);
        }
    }
    /**
     * Get a value from the model.
     * If $abstract is empty, it will return the entire model.
     * If $abstract is set, it will return the value of that key.
     */
    public function get($abstract=''):mixed{
        if($abstract){
            return $this->model[$abstract];
        }else{
            return $this->getModel();
        }
    }
        /**
     * Get the entire model.
     * This is a snapshot of the current state of the model.
     */
    public function getAll(){
        return $this->model;
    }
    /**
     * Check if a key exists in the model.
     * Returns true if the key exists, false otherwise.
     */
    public function has($abstract):bool{
        return isset($this->model[$abstract]);
    }

}

?>