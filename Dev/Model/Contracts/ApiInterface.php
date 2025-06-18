<?php 
namespace Dev\IO\Model\Contracts;

interface ApiInterface{

    public function create(string $endpoint, array $params = []): array;
    public function read(string $endpoint, array $data): array;
    public function update(string $endpoint, array $data): array;
    public function delete(string $endpoint): array;
}

?>