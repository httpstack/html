<?php

namespace HttpStack\App\Controllers\Routes;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;

//use HttpStack\Template\Template;
class LoginController
{


    public function __construct()
    {

        //i feel like here some initail view shit con be setup or pulled from the container
        //if not no biggie
    }
    public function login(Request $req, Response $res, Container $container)
    {
        $v = $container->make("view", "public/login");
        $v->render();
        if (!$res->sent) {
            $res->send();
        }
    }
}
