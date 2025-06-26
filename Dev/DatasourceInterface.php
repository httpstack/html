<?php
namespace Dev;

interface DatasourceInterface {
    /**
     * Create a new record in the datasource.
     *
     * @param string $endpoint The endpoint to create the record at.
     * @param array $params The parameters for the new record.
     * @return array The result of the create operation.
     */
    public function create(array $data): void;

    /**
     * Read records from the datasource.
     *
     * @return array The records from the datasource.
     */
    public function read(): array;

    /**
     * Update an existing record in the datasource.
     *
     * @param string $endpoint The endpoint to update the record at.
     * @param array $data The data to update the record with.
     * @return array The result of the update operation.
     */
    public function update(array $data): void;

    /**
     * Delete a record from the datasource.
     *
     * @param string $endpoint The endpoint to delete the record from.
     * @return array The result of the delete operation.
     */
    public function delete(array $data): array;
}

?>
?>