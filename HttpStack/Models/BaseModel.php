<?php
namespace HttpStack\Models;
use HttpStack\DataBase\DBConnect;
class BaseModel{
    
    protected array $model;
    public function __construct(){
        $this->model = [];
    }

    public function set(string $abstract, mixed $concrete){
        $this->model[$abstract] = $concrete;
    }
    public function get($abstract=null):mixed{
        if($abstract){`
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