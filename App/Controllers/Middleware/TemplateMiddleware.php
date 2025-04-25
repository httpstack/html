<?php
namespace App\Controllers\Middleware;
use App\_Template;
use App\Template;
class TemplateMiddleware
{
    public $container;
    protected $templatePath;
    public $response;
    public $request;


    public function init($request, $response, $container, $next)
    {
        // Initialize the template engine
        $template = new _Template($container);

        // Set the template engine in the response object
        $response->setTemplate($template);

        // Set the base template in the response object
        $response->setBaseTemplate($this->baseTemplate);



        // Call the next middleware or controller
        return $next($request);
    }
}
?>