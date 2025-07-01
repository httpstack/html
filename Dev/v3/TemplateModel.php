<?php
namespace Dev\v3;

use Dev\v3\Model\AbstractModel;
use Dev\v3\Interfaces\CRUD; // Import the CRUD interface

class TemplateModel extends AbstractModel {

    protected string $templateName;

    /**
     * Constructor for TemplateModel.
     *
     * @param CRUD $datasource The datasource for this model.
     * @param string $templateName The name of the template this model represents.
     * @param array $initialData Optional initial data for the model.
     */
    public function __construct(CRUD $datasource, string $templateName, array $initialData = []) {
        parent::__construct($datasource, $initialData); // Pass datasource and initial data to parent
        $this->templateName = $templateName;
        // Optionally, load data specific to this template name from the datasource
        // For instance, if your FileDatasource could load a specific file based on templateName
        // $loadedData = $datasource->read(['template_name' => $templateName]);
        // $this->setAll($loadedData);
    }

    // You can add template-specific methods here if needed
    public function getTemplateName(): string {
        return $this->templateName;
    }
}



?>