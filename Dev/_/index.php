<?php
require_once '/var/www/html/Dev/_/App/autoload.php';
use App\Models\TemplateModel;
use App\Datasources\FS\JsonDirectory;
use Stack\Datasource\Concrete\Datasource;
$ds = new JsonDirectory('/var/www/html/HttpStack/App/data/template',false);
$model = new TemplateModel($ds);
$model->set('test.json', ['key' => 'value']);
$model->remove('test.json'); // This should push the state before removing
$model->save();
var_dump($model->get('test.json')); // Should return ['key' => 'value']
var_dump($model->getAll()); // Should be empty initially

// Should return ['file1' => 'content1']
?>