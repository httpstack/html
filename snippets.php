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
//$myobj = new MyClass();
?>