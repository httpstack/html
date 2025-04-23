<?php
namespace App;
use App\Contracts\ConfigInterface;

/**
 * Class Config
 * @package App
 *
 * This class implements the ConfigInterface and provides methods to manage application configuration.
 */
class Config implements ConfigInterface
{
    protected array $settings = [];
    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }
    public function load(string $file): void
    {
        if (file_exists($file)) {
            $this->settings = include $file;
            if (!is_array($this->settings)) {
                throw new \Exception("Invalid configuration file format: " . $file);
            }else {
                $this->settings = array_merge($this->settings, include $file);
            }
        } else {
            throw new \Exception("Configuration file not found: " . $file);
        }
    }
    public function get(string $key): mixed
    {
        return $this->settings[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->settings[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($this->settings[$key]);
    }

    public function all(): array
    {
        return $this->settings;
    }
}   