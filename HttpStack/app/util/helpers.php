<?php
if(!function_exists("app")){
    function app(){
        if(isset($GLOBALS["app"])){
            return $GLOBALS["app"];
        }
    }
}
if(!function_exists("box")){
    function box(){

    }
}
 function dd(mixed $data){
    $debug = app()->debug;
    if($debug){
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
 }
?>