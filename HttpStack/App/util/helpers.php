<?php
if(!function_exists("app")){
    function app(){
        if(isset($GLOBALS["app"])){
            return $GLOBALS["app"];
        }
    }
}
if(!function_exists("box")){
    function box(string $make=null, array $params = []){
        // if u pass an abstract to box("myTool") it will return the resolved data
        // otherwise it just returns the container;
        return $make ? 
        // fetch container
        // make abstract and return result
        app()->getContainer()->make($make, $params):
        //fetch container
        app()->getContainer();
    }
}
 function dd(mixed $data){
    $debug = app()->debug;

    if($debug){
        app()->reportErrors();
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
 }
?>