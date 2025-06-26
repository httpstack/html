<?php
namespace Dev;
use Dev\DatasourceInterface;
class JsonDirDatasource implements \Dev\DatasourceInterface {
    protected string $file;
    protected array $properties = [];

    // on instance our datasource should fill its internal properties 
    // with data from the file or directory
    public function __construct(string $file) {
        $this->file = $file;
        if(is_dir($file)){
            foreach (glob($this->file . "/" . '*.json') as $file) {
                $filename = basename($file, '.json');
                print "filename: $filename\n";
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
        if(!$this->readOnly){
            $filename = uniqid('record_') . '.json';
            $this->properties[$filename] = $data;
            file_put_contents($this->file . $filename, json_encode($data, JSON_PRETTY_PRINT));
            return $data;
        }
        throw new \Exception("Datasource is read-only, cannot create new records.");
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