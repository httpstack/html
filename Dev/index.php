<?php 
require_once __DIR__ . '/autoload.php';

use Dev\v3\Datasources\JsonDatasource;
use Dev\v3\Model\DataModel;

    
$dsource = new JsonDatasource('/var/www/html/HttpStack/App/data/template', true);

$model = new DataModel($dsource, true);

?>