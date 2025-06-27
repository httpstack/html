<?php 
namespace Dev\v2_0;
//use Dev\v2_0\
//use Dev\v2_0\
use Dev\v2_0\CrudInterface;
use Dev\v2_0\AbstractDatasource;

class FileDatasoure extends AbstractDatasource implements CrudInterface {
    protected string $filePath;
    protected bool $isDir = false;
    protected array $data = [];
    public function __construct(string $filePath, bool $readOnly = true) {
        parent::__construct($readOnly);
        $this->isDir = is_dir($filePath);
        $this->filePath = $filePath;
        $this->defaultDataFormat = '.json';

        //a collection        
       
    }

    public function read(array $data = []): array {
         if($this->isDir) {
            $pattern = $this->filePath . "/*" . $this->defaultDataFormat;
            //this is a file datasource so it will read the file or dir
            // and return the text, the trait or filter will prepre the data
            // further
            foreach (glob($pattern) as $file) {
                $filename = basename($file, $this->defaultDataFormat);
                $this->properties[$filename] = file_get_contents($file);
            }
            //the glob pattern will be all thats changed 
            // and then the way that it prepares the data 
            // befpre setting to the dat set
        }else{
            $this->properties = file_get_contents($this->filePath) ?? [];
        }
        $prepared = $this->prepareData($this->properties);
        return $prepared;
    }
    protected function prepareData(array $rawData):array{
            $tempData = [];
            switch($this->defaultDataFormat) {
                case '.json':
                    //make a temp array and loop and json decode and reset
                    foreach ($rawData as $key => $value) {
                        $tempData[$key] = json_decode($value, true) ?? [];
                    }
                    break;
                case '.xml':
                    // Handle XML format if needed
                    foreach ($this->properties as $key => $value) {
                        $tempData[$key] = new DOMXPath(new DOMDocument($value));
                    }
                    break;
                case '.csv':
                    // Handle CSV format if needed
                    foreach ($this->properties as $key => $value) {
                        $tempData[$key] = new CSVReader($value);
                    }
                    break;
                case '.txt':
                    // Handle plain text format$value));
                    
                    break;
                default:
                    throw new \Exception("Unsupported data format: " . $this->defaultDataFormat);
            }
            return $tempData;
    }
    public function create(array $data): void {
        if ($this->isReadOnly()) {
            throw new \Exception("Cannot write to read-only datasource.");
        }
        $this->data = $data;
        file_put_contents($this->filePath, json_encode($this->data));
    }

    public function update(array $data): void {
        if ($this->isReadOnly()) {
            throw new \Exception("Cannot update in read-only mode.");
        }
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->data)) {
                $this->data[$key] = $value;
            }
        }
        file_put_contents($this->filePath, json_encode($this->data));
    }

    public function delete(string $key): void {
        if ($this->isReadOnly()) {
            throw new \Exception("Cannot delete in read-only mode.");
        }
        unset($this->data[$key]);
        file_put_contents($this->filePath, json_encode($this->data));
    }
}

?>