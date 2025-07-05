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
<!-- REACT-DOM BABEL JQUERY -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HttpStack.tech - A portfolio showcasing web development projects and skills.">
    <meta name="keywords" content="portfolio, web development, projects, skills, HttpStack">
    <meta name="author" content="HttpStack Team">
    <link rel="icon" href="public/assets/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="public/assets/images/apple-touch-icon.png">
    <title>httpstack.tech - Portfolio</title>
    <link rel="stylesheet" href="public/assets/css/styles.css">
    <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
    <script src="https://unpkg.com/babel-standalone@7.16.7/babel.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script type="text/babel" src="public/assets/js/app.js"></script>
</head>