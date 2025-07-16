<?php
namespace HttpStack\App\Views;
use Dev\Template\Template;
use HttpStack\Http\Response;

class View {


    public function __construct(protected Template $template, protected Response $response){
        $this->template = $template;
        $this->response = $response;

        $this->model = box();
    }
}
?>