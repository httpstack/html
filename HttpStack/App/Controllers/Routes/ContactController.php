<?php

namespace HttpStack\App\Controllers\Routes;

class ContactController
{
    public function index($req, $res, $container, $matches)
    {
        //do some initial stuff  here by going into the request oobject and parsing the 
        // sub routes from the uri or whatever is availble to extract it
        // then call the contact method to render the contact view
        // this is a good place to do some initial logic for the contact page
        // like checking if the user is logged in or not, or if the user has a
        // specific role, or if the user has a specific permission
        // or if the user has a specific setting enabled, or if the user has a specific
        // feature enabled, or if the user has a specific plan, or if the user has
        // a specific subscription, or if the user has a specific account type, or if the
        // user has a specific account status, or if the user has a specific account
        // or if the user has a specific account preference,

        $subRoutes = $req->getSubRoutes();
        $this->contact($req, $res, $container, $matches);
    }
    protected function contact($req, $res, $container, $matches)
    {
        $v = $container->make("view", "public/contact");
        $v->render();
        if (!$res->sent) {
            $res->send();
        }
    }
}
