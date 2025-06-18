<?php 
namespace Dev\IO\Database\Contracts;

/**
 * Interface ModelInterface
 * @package Dev\IO\Database\Contracts
 */
interface ModelInterface{
    public function __construct(array $properties = []);
    public function get(array $properties = [], array $options = []):array;
    public function getAll():array;
    public function set(array $properties):void;
    public function setAll(array $properties, array $options = []):void;
    public function remove(array $properties):void;
    public function clear():void;
}

?>