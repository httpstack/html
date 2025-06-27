<?php
namespace Dev\v2_0;

use Dev\v2_0\AttributesInterface;

class BaseModel implements AtrributesInterface{
    protected array $attributes = [];

    public function __construct(array $data = []){
        $this->attributes = $data;
    }

    /**
     * @method get(string $key) : mixed
     * Method will get a value by the given key in the dat model
     * */
    public function get(string $key):mixed{
        return $this->attributes[$key]?$this->attributes[$key]:null;
    }

    /**
     * @method getAll() : mixed
     * Method will will get the entire data model
     */
    public function getAll() : array{
        return $this->attributes;
    }

     
    /**
    * @method set(mixed $key, mixed $value) : void 
    * Method will set a key and a value in the data model
    * You can set any key tha you want, for exampe you could
    * Set
     */
    public function set(mixed $key, mixed $value):void{
        $this->attributes[$key] = $value;
    }
    public function setAll(array $data):void{
        $this->attributes = $data;
    }
    public function has(mixed $key):bool{
        return $this->attributes[$key] ? true:false;
    }
    public function clear():void{
        $this->attributes = [];
    }
    public function remove(mixed $key):void{
        if(isset($this->attributes[$key])){
            unset($this->attributes[$key]);
        }
    }
}

?>