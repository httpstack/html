<?php
namespace HttpStack\Contracts;
interface DatasourceInterface {
    public function fetch(string|array|null $key): array;
    public function save(array $data):void;
}
?>