<?php 
require_once __DIR__ . '/autoload.php';
use Dev\IO\Database\Connection\PdoConnect;
use Dev\JsonDirDatasource;
use Dev\DataModel;


$dsn = 'mysql:host=localhost;dbname=cmcintosh;charset=utf8';
$username = 'http_user';
$password = 'bf6912';
$options = [ 
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];


    try {
        $pdo = new PdoConnect($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die("Error connecting to database: " . $e->getMessage());
    }
    if ($pdo->isConnected()) {
        echo "Database connection established successfully.";
    } else {
        echo "Failed to connect to the database.";
    }
    
$jsonDS = new JsonDirDatasource('/var/www/html/HttpStack/App/data/template');
$model = new DataModel($jsonDS);
var_dump($model->getAll());
//var_dump($jsonDS->read());

?>