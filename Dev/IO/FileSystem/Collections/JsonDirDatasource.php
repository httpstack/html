<?php 
namespace Dev\IO\FileSystem\Collections;
use Dev\Contracts\CrudInterface;
use Dev\IO\FileSystem\Abstracts\AbstractDatasource;
/**
 * Class JsonDirDatasource
 * A datasource that manages JSON files in a directory.
 * Implements CRUD operations for JSON files.
 */
class JsonDirDatasource extends AbstractDatasource implements CrudInterface {
    protected $directory;
    protected $data = [];
    protected $modified = false;
    public function __construct(string $directory) {
        $this->directory = rtrim($directory, '/') . '/';
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0755, true);
        }
        //$this->loadData();    // Uncomment to load existing JSON files on initialization  
    }
    /*
    protected function loadData(): void {
        foreach (glob($this->directory . '*.json') as $file) {
            $filename = basename($file, '.json');
            $this->properties[$filename] = json_decode(file_get_contents($file), true);
        }
    }

    public function create(string $filename, array $data): void {
        file_put_contents($this->directory . $filename . '.json', json_encode($data, JSON_PRETTY_PRINT));
        $this->properties[$filename] = $data;
        $this->modified = true;
    }

    public function read(string|array $key = null): array {
        if($this->modified) {
            $this->loadData(); // Reload data if modified
            $this->modified = false;
        }
        if(is_null($key)) {
            //return all files and values
            return $this->properties;
        }elseif (is_string($key)) {
            //return specific file
            return $this->properties[$key] ?? [];
        } elseif (is_array($key)) {
            $file = $key[0];
            $property = $key[1];
            return $this->properties[$file][$property];
            //return specific files
            return array_intersect_key($this->properties, array_flip($key));
        }
    }

    public function update(string $filename, array $data): void {
        if (isset($this->properties[$filename])) {
            $this->properties[$filename] = array_merge($this->properties[$filename], $data);
            file_put_contents($this->directory . $filename . '.json', json_encode($this->properties[$filename], JSON_PRETTY_PRINT));
        }
    }

    public function delete(string $filename): void {
        unset($this->properties[$filename]);
        unlink($this->directory . $filename . '.json');
    }
    public function __destruct() {
        // Optionally, you can save the data back to files when the object is destroyed.
        foreach ($this->properties as $filename => $data) {
            file_put_contents($this->directory . $filename . '.json', json_encode($data, JSON_PRETTY_PRINT));
        }
    }
        */
}
?>