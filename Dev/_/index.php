<?php
require_once '/var/www/html/Dev/_/App/autoload.php';
use \Dev\_\App\Datasources\TestDatasource;
use \Dev\_\App\Models\Model;

$ds = new TestDatasource();
$model = new Model($ds,[]);
var_dump($model->getAll()); // Should be empty initially
?>