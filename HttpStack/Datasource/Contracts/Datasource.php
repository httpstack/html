<?php

namespace Stack\Datasource\Contracts;

interface Datasource
{
    public function setReadOnly(bool $readOnly): void;
    public function isReadOnly(): bool;  
    public function disconnect(): mixed;
}