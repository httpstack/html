<?php
namespace Dev\v2_0;

interface AttributesInterface{
    public function __construct( array $attributes = []);
    public function get($key): mixed;
    public function set($key, $value): void;
    public function getAll():array;
    public function has($key): bool;
    public function setAll(array $data): void;
    public function remove($key): void;
    public function clear(): void;
}
?>