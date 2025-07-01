<?php
namespace Dev\Contracts\Services\CrudProviderInterface;

interface CrudProviderInterface
{
    /**
     * Finds a single record by ID.
     * @param mixed $id
     * @return array|null The record data as an associative array, or null if not found.
     */
    public function find(mixed $id): ?array;

    /**
     * Finds all records.
     * @return array An array of associative arrays, each representing a record.
     */
    public function findAll(): array;

    /**
     * Creates a new record.
     * @param array $data The data to create the record.
     * @return array The created record data, including any generated IDs.
     */
    public function create(array $data): array;

    /**
     * Updates an existing record.
     * @param mixed $id The ID of the record to update.
     * @param array $data The data to update.
     * @return array The updated record data.
     */
    public function update(mixed $id, array $data): array;

    /**
     * Deletes a record by ID.
     * @param mixed $id The ID of the record to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(mixed $id): bool;
}