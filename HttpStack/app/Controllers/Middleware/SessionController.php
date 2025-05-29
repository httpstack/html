<?php
namespace HttpStack\app\Controllers\Middleware;

use HttpStack\Session\Session;
class SessionController{
    protected $session;
    public function __construct(){
        // FOR READABILITY
        // SET WITH PROPERTY PROMOTION IN CONSTRUCT
        $this->session = new Session();
        $this->session->start();
    }
     public function process($req,$res,$matches){
        $res->setHeader("Middleware", "Session Started");
     }
}
?>