<?php
namespace Dev;

class Datasource implements DatasourceInterface {
    protected string $file;
    protected array $properties = [];

    // on instance our datasource should fill its internal properties 
    // with data from the file or directory
    public function __construct(string $file) {
        $this->file = $file;
        if(is_dir($file)){
            foreach (glob($this->file . '*.json') as $file) {
                $filename = basename($file, '.json');
                $this->properties[$filename] = json_decode(file_get_contents($file), true);
            }
        }else {
            if (file_exists($this->file)) {
                $this->properties = json_decode(file_get_contents($this->file), true);
            } else {
                $this->properties = [];
            }
        }
    }
    public function create(array $data): array {
        // Implementation for creating a record
    }

    public function read(): array {
        return $this->properties;
    }

    public function update(array $data): array {
        // Implementation for updating a record
    }

    public function delete(array $data): array {
        // Implementation for deleting a record
    }
}
?>