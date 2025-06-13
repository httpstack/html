<?php 
namespace HttpStack\Abstract;
//use ;
abstract class AbstractModel{ 
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
        $this->fetch();
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
    public function set(string|array $abstract, string|array $concrete = ''){
        if(is_array($abstract)){
            foreach($abstract as $abstractKey => $concreteValue){
               $this->model[$abstractKey] = $concreteValue; 
            }
            return;
        }
        $this->model[$abstract] = $concrete;
    }
    public function remove(string $abstract):void{
        if(isset($this->model[$abstract])){
            unset($this->model[$abstract]);
        }
    }
    public function clear():void{
        $this->model = [];
    }
    public function get($abstract=''):mixed{
        if($abstract){
            return $this->model[$abstract];
        }else{
            return $this->getModel();
        }
    }
    public function has($abstract){
        return isset($this->model[$abstract]);
    }
    private function getAll(){
        return $this->model;
    }
    public function setAll(array $data):void{
        $this->model = $data;
    }
}
?>