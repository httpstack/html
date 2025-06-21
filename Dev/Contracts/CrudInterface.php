<?php 
namespace Dev\IO\Contracts;

interface CrudInterface{
    public function create(array $data = []): array;
    public function read(): array;
    public function update(string|array $key = null): array;
    public function delete(string|array $key = null): array;
}


?>