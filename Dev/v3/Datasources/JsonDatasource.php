<?php
namespace Dev\v3\Datasources;

use Dev\v3\AbstractDatasource;
use Dev\v3\Interfaces\CRUD;
use InvalidArgumentException;
use Exception;

/**
 * JsonDatasource class.
 *
 * This datasource handles JSON data from either a single file or a directory
 * containing multiple JSON files.
 *
 * If the source is a single file, it expects the file to contain either
 * a single JSON object or a JSON array of objects.
 *
 * If the source is a directory, it treats each .json file within that
 * directory as a separate record. The filename (without extension) acts as the key (ID).
 */
class JsonDatasource extends AbstractDatasource implements CRUD
{
    protected string $filePath;

    /**
     * Constructor for JsonDatasource.
     *
     * @param string $filePath The path to the JSON file or directory.
     * @param bool $readOnly Whether the datasource should be read-only.
     * @throws InvalidArgumentException If the path is invalid or inaccessible.
     */
    public function __construct(string $filePath, bool $readOnly = true)
    {
        parent::__construct($readOnly);
        $this->filePath = rtrim($filePath, '/\\'); // Normalize path, remove trailing slash

        // Ensure the path exists and is accessible
        if (!file_exists($this->filePath)) {
            // If it's a directory, try to create it.
            // If it's a file, the file will be created on first 'create' operation.
            if (!is_dir($this->filePath) && !is_file($this->filePath)) {
                $dir = dirname($this->filePath);
                if (!is_dir($dir) && !empty($dir) && $dir !== '.') {
                    if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
                        throw new InvalidArgumentException("Directory '{$dir}' could not be created.");
                    }
                }
            }
        }
    }

    /**
     * Reads data from the JSON source.
     *
     * If the source is a file:
     * - If $payload['id'] is provided and the file contains an array of objects,
     * it attempts to find the object with that ID.
     * - Otherwise, returns the entire decoded content.
     * If the source is a directory:
     * - If $payload['id'] is provided, it reads the specific file named '{id}.json'.
     * - Otherwise, it reads all .json files in the directory, using filenames as keys.
     *
     * @param array $payload Optional payload for filtering (e.g., ['id' => 123]).
     * @return mixed The fetched data (array for multiple records, object/array for single, or empty array).
     * @throws Exception If file cannot be read or JSON decoding fails.
     */
    public function read(array $payload = []): mixed
    {
        if (is_dir($this->filePath)) {
            // Directory mode
            $allData = [];
            $targetId = $payload['id'] ?? null;

            if ($targetId !== null) {
                // Read a specific file by ID
                $filePath = "{$this->filePath}/{$targetId}.json";
                if (!file_exists($filePath)) {
                    return []; // Specific file not found
                }
                $content = file_get_contents($filePath);
                if ($content === false) {
                    throw new Exception("Could not read file: {$filePath}");
                }
                $decoded = json_decode($content, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Error decoding JSON from file '{$filePath}': " . json_last_error_msg());
                }
                return $decoded; // Return the single decoded record
            } else {
                // Read all JSON files in the directory
                foreach (glob("{$this->filePath}/*.json") as $file) {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $content = file_get_contents($file);
                    if ($content === false) {
                        error_log("Could not read file: {$file}"); // Log and skip
                        continue;
                    }
                    $decoded = json_decode($content, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $allData[$filename] = $decoded;
                    } else {
                        error_log("Error decoding JSON from file '{$file}': " . json_last_error_msg()); // Log and skip
                    }
                }
                return $allData; // Return an associative array of all records
            }
        } elseif (is_file($this->filePath)) {
            // Single file mode
            if (!file_exists($this->filePath)) {
                return []; // File does not exist
            }
            $content = file_get_contents($this->filePath);
            if ($content === false) {
                throw new Exception("Could not read file: {$this->filePath}");
            }
            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Error decoding JSON from file '{$this->filePath}': " . json_last_error_msg());
            }

            // If an ID is requested, and the data is an array of records, find it.
            if (isset($payload['id']) && is_array($data)) {
                foreach ($data as $record) {
                    if (isset($record['id']) && $record['id'] == $payload['id']) {
                        return $record;
                    }
                }
                return []; // Record not found in array
            }
            return $data; // Return the entire decoded content
        } else {
            // Path is neither a file nor a directory (or doesn't exist)
            return []; // Or throw an exception if strict behavior is needed
        }
    }

    /**
     * Creates a new record in the JSON source.
     *
     * @param array $payload The data for the new record. Must contain 'id' if in directory mode.
     * @return array The created record data, including any generated ID.
     * @throws Exception If in read-only mode, or if ID is missing in directory mode.
     */
    public function create(array $payload): array
    {
        if ($this->isReadonly()) {
            throw new Exception("Cannot create: Datasource is in read-only mode.");
        }

        if (is_dir($this->filePath)) {
            // Directory mode: Create a new file named by ID
            if (!isset($payload['id'])) {
                throw new InvalidArgumentException("In directory mode, payload must contain an 'id' for file creation.");
            }
            $fileName = "{$this->filePath}/{$payload['id']}.json";
            if (file_exists($fileName)) {
                throw new Exception("Record with ID '{$payload['id']}' already exists.");
            }
            $jsonContent = json_encode($payload, JSON_PRETTY_PRINT);
            if ($jsonContent === false) {
                throw new Exception("Error encoding JSON for creation: " . json_last_error_msg());
            }
            if (file_put_contents($fileName, $jsonContent) === false) {
                throw new Exception("Failed to write new record file: {$fileName}");
            }
            return $payload; // Return the created data
        } else {
            // Single file mode: Read all, append, write all
            $currentData = $this->read();
            if (!is_array($currentData)) {
                // If the file was empty or contained a single object, convert to array for appending
                $currentData = $currentData ? [$currentData] : [];
            }

            if (!isset($payload['id'])) {
                // Generate a simple unique ID if not provided
                $payload['id'] = uniqid();
            }

            $currentData[] = $payload; // Append new record
            $jsonContent = json_encode($currentData, JSON_PRETTY_PRINT);
            if ($jsonContent === false) {
                throw new Exception("Error encoding JSON for appending: " . json_last_error_msg());
            }
            if (file_put_contents($this->filePath, $jsonContent) === false) {
                throw new Exception("Failed to write to file: {$this->filePath}");
            }
            return $payload; // Return the created data
        }
    }

    /**
     * Updates an existing record in the JSON source.
     *
     * @param array $payload The data to update. Must contain 'id'.
     * @return array The updated record data.
     * @throws Exception If in read-only mode, ID is missing, or record not found.
     */
    public function update(array $payload): array
    {
        if ($this->isReadonly()) {
            throw new Exception("Cannot update: Datasource is in read-only mode.");
        }
        if (!isset($payload['id'])) {
            throw new InvalidArgumentException("Payload must contain an 'id' to update a record.");
        }

        $idToUpdate = $payload['id'];

        if (is_dir($this->filePath)) {
            // Directory mode: Update specific file
            $fileName = "{$this->filePath}/{$idToUpdate}.json";
            if (!file_exists($fileName)) {
                throw new Exception("Record with ID '{$idToUpdate}' not found for update.");
            }
            $jsonContent = json_encode($payload, JSON_PRETTY_PRINT);
            if ($jsonContent === false) {
                throw new Exception("Error encoding JSON for update: " . json_last_error_msg());
            }
            if (file_put_contents($fileName, $jsonContent) === false) {
                throw new Exception("Failed to write updated record file: {$fileName}");
            }
            return $payload; // Return the updated payload
        } else {
            // Single file mode: Read all, find and modify, write all
            $currentData = $this->read();
            if (!is_array($currentData)) {
                throw new Exception("Data format invalid for update in single file mode. Expected array of records.");
            }
            $found = false;
            foreach ($currentData as &$record) { // Use reference to modify directly
                if (isset($record['id']) && $record['id'] == $idToUpdate) {
                    $record = array_merge($record, $payload); // Merge new data into existing record
                    $found = true;
                    break;
                }
            }
            unset($record); // Break the reference

            if (!$found) {
                throw new Exception("Record with ID '{$idToUpdate}' not found for update.");
            }
            $jsonContent = json_encode($currentData, JSON_PRETTY_PRINT);
            if ($jsonContent === false) {
                throw new Exception("Error encoding JSON after update: " . json_last_error_msg());
            }
            if (file_put_contents($this->filePath, $jsonContent) === false) {
                throw new Exception("Failed to write updated data to file: {$this->filePath}");
            }
            return $payload; // Return the updated payload
        }
    }

    /**
     * Deletes a record from the JSON source.
     *
     * @param array $payload The data to identify the record (must contain 'id').
     * @return bool True on success, false on failure.
     * @throws Exception If in read-only mode, ID is missing, or deletion fails.
     */
    public function delete(array $payload): bool
    {
        if ($this->isReadonly()) {
            throw new Exception("Cannot delete: Datasource is in read-only mode.");
        }
        if (!isset($payload['id'])) {
            throw new InvalidArgumentException("Payload must contain an 'id' to delete a record.");
        }

        $idToDelete = $payload['id'];

        if (is_dir($this->filePath)) {
            // Directory mode: Delete specific file
            $fileName = "{$this->filePath}/{$idToDelete}.json";
            if (file_exists($fileName)) {
                if (!unlink($fileName)) {
                    throw new Exception("Failed to delete file: {$fileName}");
                }
                return true;
            }
            return false; // File not found
        } else {
            // Single file mode: Read all, remove, write all
            $currentData = $this->read();
            if (!is_array($currentData)) {
                throw new Exception("Data format invalid for delete in single file mode. Expected array of records.");
            }
            $newData = [];
            $deleted = false;
            foreach ($currentData as $record) {
                if (isset($record['id']) && $record['id'] == $idToDelete) {
                    $deleted = true;
                } else {
                    $newData[] = $record;
                }
            }

            if ($deleted) {
                $jsonContent = json_encode($newData, JSON_PRETTY_PRINT);
                if ($jsonContent === false) {
                    throw new Exception("Error encoding JSON after deletion: " . json_last_error_msg());
                }
                if (file_put_contents($this->filePath, $jsonContent) === false) {
                    throw new Exception("Failed to write updated data to file after deletion: {$this->filePath}");
                }
            }
            return $deleted;
        }
    }
}