<?php
namespace App\Datasources\Contracts;
interface CRUD{
    public function create(array $payload):mixed;
    public function read(array $payload = []):mixed;
    public function update(array $payload):mixed;
    public function delete(array $payload):mixed;
}
?>