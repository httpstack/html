<?php 
namespace HttpStack\Abstract;
//use ;
abstract class AbstractModel{ 
    // if the p-roperty is declarted ABSTRACT 
    // then that means the propertrty must exist but its
    // implementation must be decloarted in a subclass
    protected array $model;
    protected array $_model;

    public function getModel(string $key = ''):mixed{
      return ($key) ? $this->fetch[$key] :$this->model;
    }
    
    abstract function fetch(mixed $var):array;
    
}
?>