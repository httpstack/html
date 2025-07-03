<?php
require_once '/var/www/html/Dev/_/App/autoload.php';
use App\Datasources\FolderDatasource;
use App\Models\Model;

$ds = new FolderDatasource();
$model = new Model($ds,[]);
var_dump($model->getAll()); // Should be empty initially
?>