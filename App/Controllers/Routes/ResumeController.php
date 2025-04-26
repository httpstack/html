<?php
namespace App\Controllers\Routes;
class ResumeController
{
    public function __invoke($request, $response, $container)
    {
        $response->setBody('<h1>Welcome to My Resume</h1>');
        $response->send();
    }
}