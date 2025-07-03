<?php
require_once '/var/www/html/Dev/_/App/autoload.php';
use App\Datasources\JsonDirDatasource;
use App\Models\TemplateModel;
$config = [
    'crudHandlers' => [
        'read' => function(string $endPoint, array $keys, callable $filter) {
            $files = [];
            foreach(scandir($endPoint) as $file) {
            
                if ($file !== '.' && $file !== '..') {
                    $absPath = realpath($endPoint . '/' . $file);
                    if(is_file($absPath)){
                       
                        $data = json_decode(file_get_contents($absPath), true); 
                        if($filter)
                        {
                            $data = array_filter($data, $filter);
                        }
                        if (empty($keys) || array_intersect_key($data, array_flip($keys))) {
                            $files[$file] = $data;
                        }
                    }

                }
            }
            return [];
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
    'endPoint' => '/var/www/html/HttpStack/App/data/template'
];
$ds = new JsonDirDatasource($config);
$model = new TemplateModel($ds);
var_dump($model->getAll()); // Should be empty initially
?>