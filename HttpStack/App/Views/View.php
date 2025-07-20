<?php
namespace HttpStack\App\Views;

use Dev\Template\Template;
use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HtppStack\Container\Container;
use HttpStack\App\Models\TemplateModel;

class View {

    protected Template $template;
    protected TemplateModel $dataModel;

    public function __construct(TemplateModel $dataModel){
        // make sure templateModel is foing the logic of preparing the model since 
        // it is the concrete model 
        //box(abstract) is a helper for $container->make(abstract);
        $this->template = box("template");
        $this->template->load($dataModel->get("baseTemplatePath"));
        $assets = $dataModel->get("assets");

        $this->template->bindAssets($assets);
        $this->template->setVariables($dataModel->getAll()['base.json']);
        $this->template->setVariables(["links" => $dataModel->getLinks("main")]);
        
    }
}
?>