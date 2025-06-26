<?php 
namespace Dev\v2_0;
 interface IF_Attributes{
        public function get(string $key): mixed;
        public function set(string $key, mixed $value): void;
        public function has(string $key): bool;
        public function remove(string $key): void;
        public function clear(): void;
        public function getAll(): array;
        public function setAll(array $data): void;
 }
 ?>

?>