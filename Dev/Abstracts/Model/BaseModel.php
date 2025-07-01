<?php
namespace Dev\Abstracts\Model;
use Dev\Contracts\Services\CrudProviderInterface;
use Dev\Contracts\Model\AttributesInterface;
abstract class BaseModel
{
    protected AttributesInterface $attributes;
    protected CrudProviderInterface $provider;
    protected array $originalAttributes = []; // For dirty tracking

    // Constructor with Dependency Injection
    public function __construct(CrudProviderInterface $provider, ?array $initialData = null)
    {
        // Assuming your AttributesInterface is implemented by a concrete class like ArrayAttributes
        $this->attributes = new ArrayAttributes(); // Or inject this as well
        $this->provider = $provider;

        if ($initialData) {
            $this->fill($initialData);
            $this->markAsClean(); // Initially, all attributes are clean
        }
    }

    /**
     * Fills the model's attributes.
     * @param array $data
     * @return $this
     */
    public function fill(array $data): self
    {
        $this->attributes->fill($data);
        return $this;
    }

    /**
     * Get an attribute.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes->get($key, $default);
    }

    /**
     * Set an attribute. Marks the attribute as dirty.
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, mixed $value): self
    {
        if ($this->attributes->get($key) !== $value) { // Only mark dirty if value changed
            $this->attributes->set($key, $value);
            // In your Attributes implementation, you'd track dirty keys
        }
        return $this;
    }

    /**
     * Get all attributes.
     * @return array
     */
    public function getAll(): array
    {
        return $this->attributes->getAll();
    }

    /**
     * Find a model by its ID.
     * @param mixed $id
     * @return static|null
     */
    public static function find(CrudProviderInterface $provider, mixed $id): ?static
    {
        $data = $provider->find($id);
        if ($data) {
            $model = new static($provider, $data); // Pass provider and data
            return $model;
        }
        return null;
    }

    /**
     * Save the model to the data source.
     * Determines if it's a create or update operation.
     * @return bool True on success, false on failure.
     */
    public function save(): bool
    {
        $id = $this->get('id'); // Assuming 'id' is your primary key

        try {
            if ($id !== null) { // Update existing
                // If you have dirty tracking in Attributes, get only dirty
                $updatedData = $this->attributes->getAll(); // Or getDirty() if implemented
                $result = $this->provider->update($id, $updatedData);
            } else { // Create new
                $createdData = $this->provider->create($this->attributes->getAll());
                // Update model with new ID and any other generated data
                $this->fill($createdData);
            }
            $this->markAsClean(); // After successful save, all attributes are clean
            return true;
        } catch (Exception $e) {
            // Log error, re-throw, or handle gracefully
            return false;
        }
    }

    /**
     * Delete the model from the data source.
     * @return bool True on success, false on failure.
     */
    public function delete(): bool
    {
        $id = $this->get('id');
        if ($id !== null) {
            try {
                return $this->provider->delete($id);
            } catch (Exception $e) {
                // Handle error
                return false;
            }
        }
        return false; // Cannot delete without an ID
    }

    // --- Dirty Tracking Helper Methods (could be in Attributes class) ---
    protected function markAsClean(): void
    {
        $this->originalAttributes = $this->attributes->getAll();
        // If Attributes class handles dirty state, call its method
        // $this->attributes->markAsClean();
    }

    public function isDirty(): bool
    {
        // Compare current attributes with originalAttributes
        return $this->attributes->getAll() !== $this->originalAttributes;
        // Or delegate to Attributes class: $this->attributes->isDirty();
    }
}