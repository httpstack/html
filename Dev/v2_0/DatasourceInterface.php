<?php
namespace Dev\v2_0;
//use Dev\v2_0\
//use Dev\v2_0\a
interface DatasourceInterface {
    public function __construct(bool $readOnly = true);
    public function isReadOnly(): bool;
    public function setReadOnly(bool $readOnly): void;
}

?>