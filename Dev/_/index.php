<?php
require_once '/var/www/html/Dev/_/App/autoload.php';
use App\Models\TemplateModel;
use Stack\Datasource\Concrete\Datasource;
$config = [
    'crudHandlers' => [
        'read' => function(string $endPoint, array $keys, ?callable $filter) {
            $files = [];
            foreach(scandir($endPoint) as $file) {
                if ($file !== '.' && $file !== '..') {
                    $absPath = realpath($endPoint . '/' . $file);
                    echo $absPath;
                    if(is_file($absPath)){
                       $files[$file] = file_get_contents($absPath);
                    }
                }
            }
            if(count($keys) > 0){
                $files = array_intersect_key($files, array_flip($keys));
            }
            if($filter){
               return $filter($files) ;
            } 
            return $files;
        },
        'create' => function($data) {
            // Simulate creating data in a JSON directory
            return true;
        },
        'update' => function($data) {
            // Simulate updating data in a JSON directory
            return true;
        },
        'delete' => function($data) {
            // Simulate deleting data in a JSON directory
            return true;
        }
    ],
    'readOnly' => false,
    'endPoint' => '/var/www/html/HttpStack/App/data/template',
    'formatFilter' => function($data) {
        // Example filter function to format data
        foreach ($data as $key => $value) {
            // You can modify the data here if needed
            // For example, you might want to decode JSON strings
            if (is_string($value) && strpos($value, '{') === 0) {
                $data[$key] = json_decode($value, true);
            }
        }
        return $data;
    }
];
$ds = new Datasource($config);
$model = new TemplateModel($ds);
$obj = $ds->read(['assets.json']);
var_dump($obj); // Should be empty initially

// Should return ['file1' => 'content1']
?>