<?php
namespace HttpStack\Models;
use HttpStack\Contracts\DatasourceInterface;
use HttpStack\DataBase\DBConnect;
class BaseModel{
    
    protected array $model;
    protected array $_model;
    /**
     * The __construct can have optional param for datasources
     * $dataSource will need these key => values
     * type - database|file|folder|object
     * 
     */
    public function __construct(protected DatasourceInterface $dataSource){
        $this->model = [];
        $this->_model = [];
        $this->fetch(null);
    }
    protected function fetch(string|array|null $key){
        $data = $this->dataSource->fetch($key);
        $this->set($data, null);
    }
    protected function save():void{   
            $this->store();
            echo "<br/>basemodel,escalating to datasource <br/>";
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
    public function set(string|array $abstract, string|array|null $concrete){
        if(is_array($abstract)){
            foreach($abstract as $abstractKey => $concreteValue){
               $this->model[$abstractKey] = $concreteValue; 
            }
            return;
        }
        $this->model[$abstract] = $concrete;
    }
    public function get($abstract=null):mixed{
        if($abstract){
            return $this->model[$abstract];
        }else{
            return $this->getModel();
        }
    }
    public function has($abstract){
        return isset($this->model[$abstract]);
    }
    private function getModel(){
        return $this->model;
    }
}

?>