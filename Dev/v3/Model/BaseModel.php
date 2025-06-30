<?php
namespace Dev\v3\Model;

use Dev\v3\Interfaces\Attributes;

class BaseModel implements Attributes{
  protected array $attributes = [];
    public function __construct(array $arrData = []){
        $this->setAll($arrData);
    }
    public function set(string $strKey, mixed $mixValue): void{
        $this->attributes[$strKey] = $mixValue;
    }

    public function get(string $strKey): mixed{
        return $this->attributes[$strKey] ?? null;
    }

    public function has(string $strKey): bool{
        return array_key_exists($strKey, $this->attributes);
    }
    public function remove(string $strKey): void{
        unset($this->attributes[$strKey]);
    }

    public function getAll(): array{
        return $this->attributes;
    }

    public function clear(): void {
        $this->attributes = [];
    }

    public function setAll(array $arrData): void{
        foreach ($arrData as $key => $value) {
            $this->set($key, $value);
        }
    }

}

?>