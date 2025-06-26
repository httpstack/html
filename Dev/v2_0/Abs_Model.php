<?php 
namespace Dev\v2_0;
use Dev\v2_0\IF_Atrributes;
use Dev\v2_0\IF_AtrributeState;
abstract class Abs_Model implements IF_Atrributes, IF_AtrributeState {
    protected array $originalData = [];
    protected bool $readOnly = true;

    public function __construct(protected \Dev\v2_0\DatasourceInterface $datasource) {
        // Initialization code if needed
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
// End of Dev/v2_0/Abs_Model.php
?>