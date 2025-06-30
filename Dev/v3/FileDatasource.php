<?php
namespace Dev\v3;
use Dev\v3\AbstractDatasource;

class FileDatasource extends AbstractDatasource
{
    protected string $filePath;

    public function __construct(string $filePath, bool $readOnly = true)
    {
        parent::__construct($readOnly);
        $this->filePath = $filePath;
    }

    public function create(array $payload): mixed
    {
        if ($this->isReadonly()) {
            throw new \Exception("Cannot create in read-only mode.");
        }
        // Logic to write to the file
        return true; // Return appropriate value based on operation
    }

    public function read(array $payload = []): mixed
    {
        // Logic to read from the file
        return file_get_contents($this->filePath);
    }

    public function update(array $payload): mixed
    {
        if ($this->isReadonly()) {
            throw new \Exception("Cannot update in read-only mode.");
        }
        // Logic to update the file
        return true; // Return appropriate value based on operation
    }

    public function delete(array $payload): mixed
    {
        if ($this->isReadonly()) {
            throw new \Exception("Cannot delete in read-only mode.");
        }
        // Logic to delete the file or its contents
        return true; // Return appropriate value based on operation
    }
}

?>