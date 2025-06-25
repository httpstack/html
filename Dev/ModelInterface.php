<?php
namespace Dev;

interface ModelInterface{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value): void;
    public function getAll(): array;
    public function setAll(array $data): void;
    public function remove(string $key): void;
    public function clear(): void;
}
?>