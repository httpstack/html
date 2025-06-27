<?php 
namespace Dev\v2_0;
use Dev\v2_0\DatasourceInterface;
use Dev\v2_0\CrudInterface;
//use Dev\v2_0\
//use Dev\v2_0\
class AbstractDatasource implements DatasourceInterface, CrudInterface { 
    protected array $data = [];

    public function __construct(protected bool $readOnly = true) {
       /**
        * Constructor will take a bool value for readOnly
        * Used property promotion so setting the instance
        * var for clarity
        *
        * @requiredby isReadOnly() and setReadOnly()
        */
        $this->readOnly = $readOnly;
    }
    /**
     * * @@interface DatasourceInterface
     * * Methods required by the DatasourceInterface interface
     * * @method isReadOnly() : bool
     * * @method setReadOnly(bool $readOnly) : void
     */
    public function isReadOnly(): bool {
        return $this->readOnly;
    }

    public function setReadOnly(bool $readOnly): void {
        $this->readOnly = $readOnly;
    }

    /**
     * * @@interface CrudInterface
     * * Methods required by the CrudInterface interface
     * * @method create(array $data) : void
     * * @method update(array $data) : void
     * * @method delete(string $key) : void
     * * @method read(array $data = []) : array
     */
    public function read(array $data = []): array {
        return $this->data;
    }

    public function create(array $data): void {
        $this->data = $data;
    }

    public function update(array $data): void {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->data)) {
                $this->data[$key] = $value;
            }
        }
    }
    public function delete(string $key): void {
        unset($this->data[$key]);
    }
}
?>