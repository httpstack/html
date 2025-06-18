<?php 
namespace Dev\Model\Abstracts;

use Dev\Model\Contracts\ModelInterface;
abstract class AbstractModel implements ModelInterface{
    protected $properties = [];
    protected $_properties = [];

    public function __construct(array $properties = []){
        $this->properties = $properties;
        $this->_properties = $properties;
    }

    public function get(array|string $properties = []): array {
        if (is_string($properties)) {
            return $this->properties[$properties] ?? null;
        }
        if (is_array($properties)) {
            return array_intersect_key($this->properties, array_flip($properties));
        }
        return $this->properties;
    }

    public function getAll(): array {
        return $this->properties;
    }   

    public function set(array $properties): void {
        foreach ($properties as $key => $value) {
            $this->properties[$key] = $value;
        }
    }
    public function setAll(array $properties): void {
        $this->properties = array_merge($this->properties, $properties);
    }
    public function remove(array|string $properties): void {
        if (is_string($properties)) {
            unset($this->properties[$properties]);
        } elseif (is_array($properties)) {
            foreach ($properties as $property) {
                unset($this->properties[$property]);
            }
        }
    }
    public function clear(): void {
        $this->properties = [];
        $this->_properties = [];
    }

    abstract public function create(): array;


}
?>