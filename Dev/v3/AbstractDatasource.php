<?php
namespace Dev\v3;

use Dev\v3\Interfaces\Datasource;
use Dev\v3\Interfaces\CRUD;

abstract class AbstractDatasource implements Datasource, CRUD
{
    protected bool $readOnly = false;

    public function __construct(bool $readOnly = true)
    {
        $this->readOnly = $readOnly;
    }

    public function isReadonly(): bool
    {
        return $this->readOnly;
    }

    public function setReadOnly(bool $readonly): void
    {
        $this->readOnly = $readonly;
    }

    abstract public function create(array $payload): mixed;
    abstract public function read(array $payload = []): mixed;
    abstract public function update(array $payload): mixed;
    abstract public function delete(array $payload): mixed;
    // Additional methods can be added here as needed
    // For example, methods for managing states or attributes can be defined
    // if this class is extended by a class that implements AttributeState.
}
?>