<?php
namespace HttpStack\App\Views;

use Dev\Template\Template;
use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HtppStack\Container\Container;
use HttpStack\App\Models\TemplateModel;
use HttpStack\Model\AbstractModel;

class View {

    protected Template $template;
    protected TemplateModel $dataModel;

    public function __construct( $dataModel){
        // make sure templateModel is foing the logic of preparing the model since 
        // it is the concrete model 
        //box(abstract) is a helper for $container->make(abstract);
        $this->template = box("template");
        $dataModel = box("template.model");
        $this->template->load($dataModel->get("baseTemplatePath"));
        $assets = $dataModel->get("assets");

        $this->template->bindAssets($assets);
        $this->template->setVariables($dataModel->getAll()['base.json']);
        //the template will be using data- the template will be itterating
        // a "links" array
        $this->template->setVariables(["links" => $dataModel->getLinks("main")]);


        //example of defining a function with parameter
        $this->template->define("myFunc", function($myparam){
            return $myparam;
        });
    }

    public function importView(string $filePath){
        $this->template->importView($filePath);
    }
}
?>