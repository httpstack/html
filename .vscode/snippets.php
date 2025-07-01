<?php
$router->before(".*", function($req) use($response){
    $response->setHeader("x-code","x-value");
});
$router->get("/home/{id}", function($req,$res,$arr){
    print_r($arr);
});
$router->get("/home", function($req, $res){
    $res->setBody("Home Page"); 
    $res->send();
});
$router->post("/home", function($req, $res){
    print_r($_POST);  
});
$router->dispatch($request, $response);



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
//$myobj = new MyClass();
?>