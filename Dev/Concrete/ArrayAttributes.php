<?php
namespace Dev\Concrete;
use Dev\Contracts\Model\AttributesInterface;

// Example of a concrete Attributes implementation (simple array)
class ArrayAttributes implements AttributesInterface
{
    private array $data = [];
    private array $dirtyKeys = []; // For simple dirty tracking

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        if (!isset($this->data[$key]) || $this->data[$key] !== $value) {
            $this->dirtyKeys[$key] = true;
        }
        $this->data[$key] = $value;
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function fill(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value); // This will mark all as dirty initially
        }
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function getDirty(): array
    {
        $dirtyData = [];
        foreach ($this->dirtyKeys as $key => $isDirty) {
            if ($isDirty) {
                $dirtyData[$key] = $this->data[$key];
            }
        }
        return $dirtyData;
    }

    public function markAsClean(): void
    {
        $this->dirtyKeys = [];
    }

    public function isDirty(string $key = null): bool
    {
        if ($key !== null) {
            return isset($this->dirtyKeys[$key]);
        }
        return !empty($this->dirtyKeys);
    }
}


// Example concrete model extending the base


// Example Provider (e.g., for a database table)