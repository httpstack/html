<?php
namespace Dev;
use Dev\DatasourceInterface;
class JsonDirDatasource implements DatasourceInterface {
    protected string $file;
    protected array $properties = [];

    // on instance our datasource should fill its internal properties 
    // with data from the file or directory
    public function __construct(string $file) {
        $this->file = $file;

    }
    public function create(array $data): void {
        if (!$this->readOnly) {
            list($file,$json) = $data;
            $filePath = $this->file . '/' . $file . '.json';
            if (!file_exists($filePath)) {
                file_put_contents($filePath, json_encode($json, JSON_PRETTY_PRINT));
            }
        } else {
           throw new \Exception("Datasource is read-only, cannot create new records.");
        }
    }

    public function read(): array {
        if(is_dir($this->file)){
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
        return $this->properties;
    }

    public function update(array $data): void {
        // Implementation for updating a record
        foreach ($data as $filename => $content) {
            $file = "{$this->file}/{$filename}.json";
            file_put_contents($file, json_encode($content, 128));
        }
    }

    public function delete(array $data): array {
        // Implementation for deleting a record
    }
}
?>