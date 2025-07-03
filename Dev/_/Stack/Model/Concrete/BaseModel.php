<?php
namespace Stack\Model\Concrete;


use Stack\Model\Concrete\KeyStore;

class BaseModel{
    protected KeyStore $attributes;

    public function __construct(array $arrData = []){
        $this->attributes = new KeyStore();
        $this->setAll($arrData);
    }

    public function set(string $strKey, mixed $mixValue): void{
        $this->attributes->set($strKey, $mixValue);
    }

    public function get(string $strKey): mixed{
        return $this->attributes->get($strKey);
    }

    public function has(string $strKey): bool{
        return $this->attributes->has($strKey);
    }

    public function remove(string $strKey): void{
        $this->attributes->remove($strKey);
    }

    public function getAll(): array{
        return $this->attributes->getAll();
    }

    public function clear(): void {
        $this->attributes->clear();
    }

    public function setAll(array $arrData): void{
        $this->attributes->setAll($arrData);
    }   

    public function count(): int {
        return $this->attributes->count();
    }
}
?>