<?php
namespace HttpStack\App\Models;

use HttpStack\Model\AbstractModel;
use HttpStack\Datasource\Contracts\CRUD; 
use HttpStack\App\Datasources\FS\JsonDirectory;
//Import your specific datasource

class TemplateModel extends AbstractModel
{
    /**
     * @var JsonDirectory The specific datasource for JSON file operations.
     */
    protected CRUD $datasource; // Type-hint specifically for JsonDirectory
    public array $data = []; // This will hold the current state of the model
    /**
     * The associative array of data to be synchronized: ['filename' => ['key' => 'value'], ...]
     * This holds the "desired state" of the JSON files.
     * The values are arrays (decoded JSON), not raw JSON strings.
     */
    protected array $incomingSyncData = [];

    /**
     * Constructor for the Model class.
     *
     * @param JsonDirectory $datasource The datasource for this model (must be a JsonDirectory instance).
     */
    public function __construct(JsonDirectory $datasource)
    {
        parent::__construct($datasource);
        
        // Explicitly cast or ensure it's a JsonDirectory datasource
        if (!$datasource instanceof JsonDirectory) {
            throw new \InvalidArgumentException("TemplateModel requires a JsonDirectory datasource instance.");
        }
       // $this->data = $this->getAll(); // Initialize with existing data from the datasource
    }

    /**
     * Sets the incoming data for file synchronization.
     * This data represents the desired state of the JSON files on disk.
     *
     * @param array $data Associative array where keys are filenames and values are arrays (decoded JSON).
     * @return self
     */
    public function setIncomingSyncData(): self
    {
        $this->incomingSyncData = $this->getAll();
        return $this;
    }
    /**
     * Performs the file synchronization: creates new JSON files, updates changed JSON files,
     * and deletes JSON files not present in the incoming data.
     *
     * @return array An associative array detailing the operations performed:
     * ['created' => [], 'updated' => [], 'deleted' => [], 'skipped' => [], 'errors' => []]
     */
    public function save(): array
    {
        $results = [
            'created' => [],
            'updated' => [],
            'deleted' => [],
            'skipped' => [],
            'errors' => []
        ];
        $this->incomingSyncData = $this->getAll();
        // 1. Get Current File List and their Contents from the datasource
        // The read() method returns filename => decoded_json_array
        $existingFilesData = $this->datasource->read();

        // Create a map for quick lookups and to track files processed
        // Using array_keys for the file names from existing data
        $filesToProcess = array_flip(array_keys($existingFilesData));

        // 2. Iterate Through Incoming Data (Create/Update)
        foreach ($this->incomingSyncData as $filename => $newDataArray) {
            try {
                if (array_key_exists($filename, $existingFilesData)) {
                    // File exists, get its current decoded content
                    $currentDataArray = $existingFilesData[$filename];

                    // Compare contents: convert arrays to JSON strings for robust comparison
                    // (Handles cases where key order might differ but content is same)
                    $currentJson = json_encode($currentDataArray, 128 | 64);
                    $newJson = json_encode($newDataArray, 128 | 64);

                    if ($currentJson !== $newJson) {
                        // Content is different, perform an update
                        if ($this->datasource->update([$filename, $newDataArray])) {
                            $results['updated'][] = $filename;
                        } else {
                            $results['errors'][] = "Failed to update file: {$filename}";
                        }
                    } else {
                        // Content is the same, no action needed
                        $results['skipped'][] = "File content identical, skipped update: {$filename}";
                    }
                    // Mark as processed from the existing files list
                    unset($filesToProcess[$filename]);
                } else {
                    // File does not exist (Create)
                    if ($this->datasource->create([$filename, $newDataArray])) {
                        $results['created'][] = $filename;
                    } else {
                        $results['errors'][] = "Failed to create file: {$filename}";
                    }
                }
            } catch (\Exception $e) {
                // Catch exceptions from datasource methods
                $results['errors'][] = "Error processing {$filename}: " . $e->getMessage();
            }
        }

        // 3. Identify and Delete Remaining Files (those in $existingFilesData but not in $this->incomingSyncData)
        foreach ($filesToProcess as $filename => $value) {
            try {
                if ($this->datasource->delete([$filename])) {
                    $results['deleted'][] = $filename;
                } else {
                    $results['errors'][] = "Failed to delete file: {$filename}";
                }
            } catch (\Exception $e) {
                // Catch exceptions from datasource methods
                $results['errors'][] = "Error deleting {$filename}: " . $e->getMessage();
            }
        }

        return $results;
    }
}
?>