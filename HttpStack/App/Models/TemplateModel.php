<?php
namespace HttpStack\App\Models;

use Stringable;
use HttpStack\Model\AbstractModel; 
use HttpStack\Datasource\Contracts\CRUD;
//Import your specific datasource

use HttpStack\App\Datasources\FS\JsonDirectory; // For __toString() if desired

/**
 * TemplateModel class.
 * Represents the data specific to a template, providing structured access
 * to common template data elements like assets and links.
 */
class TemplateModel extends AbstractModel implements Stringable // Added Stringable for demonstration
{
    protected string $templateName;

    /**
     * Constructor for TemplateModel.
     *
     * @param CRUD $datasource The datasource for this model.
     * @param string $templateName The logical name or identifier for this template's data.
     * @param array $initialData Optional initial data for the model.
     */
    public function __construct(CRUD $datasource, string $templateName, array $initialData = [])
    {
        parent::__construct($datasource, $initialData);

        $this->templateName = $templateName;
    }

    /**
     * Returns the logical name of this template's data.
     *
     * @return string
     */
    public function getTemplateName(): string
    {
        return $this->templateName;
    }
    public function getLinks($which){
        return $this->getAll()['links.json'][$which];
    }
    /**
     * Retrieves the 'assets' array from the model's data.
     *
     * @return array An array of asset definitions, or an empty array if not found.
     */
    public function getAssets(): array
    {
        return $this->get('assets') ?? [];
    }

    /**
     * Retrieves a specific asset by its name from the 'assets' array.
     *
     * @param string $assetName The name of the asset to retrieve.
     * @return array|null The asset definition array, or null if not found.
     */
    public function getAsset(string $assetName): ?array
    {
        $assets = $this->getAssets();
        foreach ($assets as $asset) {
            if (isset($asset['name']) && $asset['name'] === $assetName) {
                return $asset;
            }
        }
        return null;
    }



    /**
     * String representation of the TemplateModel.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "TemplateModel: " . $this->getTemplateName() . " (ID: " . ($this->get('id') ?? 'N/A') . ")";
    }
}