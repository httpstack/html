<?php
namespace Base\App\Controllers\Middleware;
use Base\App\_Template;
class TemplateMiddleware
{
    protected $container;
    protected $templatePath;
    public $response;
    public $request;
    public function __construct($request, $response, $container, $next)
    {
        $this->container = $container;

        $this->templatePath = $this->container->make('config',['template']);
        $this->response = $response;
        $this->request = $request;
    }

    public function handle($request, $next)
    {
        // Set the template path in the container
        $this->container->bind('template.path', $this->templatePath);

        // Call the next middleware or controller
        return $next($request);
    }
}
?>