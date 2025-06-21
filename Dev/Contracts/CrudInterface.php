<?php 
namespace Dev\IO\Contracts;

interface CrudInterface{
    public function create(array $data = []): array;
    public function read(array $key = null): array;
    public function update(array $key = null): array;
    public function delete(array $key = null): array;
}


?>