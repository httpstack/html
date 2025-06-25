<?php
namespace Dev;

use Dev\Model;

abstract class AbstractDataModel extends Model{
    protected array $originalData = [];
    protected bool $readOnly = true;

    public function __construct(protected DatsourceInterface $datasource) {
        parent::__construct($data);
        $this->originalData = $data;
    }
    public function isReadOnly(): bool {
        return $this->readOnly;
    }
    public function setReadOnly(bool $value): void {
        $this->readOnly = $value;
    }
    public function getOriginalData(): array {
        return $this->originalData;
    }
}
?>