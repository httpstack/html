<?php
namespace Dev;
use Dev\ModelInterface;
class Model implements ModelInterface {
    protected array $data = [];

    public function __construct() {
        
    }
    
    public function get(string $key): mixed {
        return $this->data[$key] ?? null;
    }

    public function set(string $key, mixed $value): void {
        $this->data[$key] = $value;
    }

    public function getAll(): array {
        return $this->data;
    }

    public function setAll(array $data): void {
        $this->data = $data;
    }

    public function remove(string $key): void {
        unset($this->data[$key]);
    }

    public function clear(): void {
        $this->data = [];
    }
}   
?>