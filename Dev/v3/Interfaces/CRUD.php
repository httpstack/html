<?php
namespace Dev\v3\Interfaces;
interface CRUD{
    public function create(array $payload):mixed;
    public function read(array $payload = []):mixed;
    public function update(array $payload):mixed;
    public function delete(array $payload):mixed;
}
?>