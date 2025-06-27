<?php 
interface CrudInterface {
    public function read(array $data = []): array;
    public function create(array $data): void;
    public function update(array $data): void;
    public function delete(array $data = []): void;
}
?>