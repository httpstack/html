<?php

namespace HttpStack\App\Views;


use \DocumentFragment;
use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\IO\FileLoader;
use HttpStack\Template\Template;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\TemplateModel;

class View
{

    protected Template $template;
    protected Response $response;
    protected Request $request;
    protected string $view;
    protected Container $container;

    public function __construct(Container $c)
    {
        // make sure templateModel is foing the logic of preparing the model since 
        // it is the concrete model 
        //box(abstract) is a helper for $container->make(abstract);
        $this->response = $c->make(Response::class);
        $this->request = $c->make(Request::class);
        $this->template = $c->make(Template::class);
        $this->container = $c;
    }
    public function loadView()
    {

        $viewRoute = $this->container->make("viewRoute");
        echo "View route: " . get_class($viewRoute) . "\n";
        $fl = $this->container->make(FileLoader::class);
        $p = $fl->findFile($viewRoute, null, "html");
        echo "View found at: $p\n";
    }
    protected function toDomObject($str)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($str, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING | LIBXML_NOERROR);
        return $dom;
    }

    public function render()
    {
        $html = $this->template->render();
        $this->response->setContentType("text/html")->setBody($html);
    }
    public function getView()
    {
        return $this->view;
    }
    public function getTemplate()
    {
        return $this->template;
    }
    public function importView(string $filePath)
    {
        $this->template->importView($filePath);
    }
}
