<?php
namespace App\IO;

class FileLoader {
    private array $files = []; // Stores files with filename as key and path as value
    private array $dirs = [];  // Stores the directory structure

    public function __construct(private array $initialDirs = []) {
        $this->addDirs($initialDirs);
        $this->loadDirs();
    }

    /**
     * Adds directories to the $dirs array.
     */
    public function addDirs(array $dirs): void {
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $this->dirs[$dir] = []; // Initialize the directory structure
            }
        }
    }

    /**
     * Processes the $dirs array and populates it with files and subdirectories.
     */
    public function loadDirs(): void {
        foreach ($this->dirs as $dir => &$contents) {
            $this->processDir($dir, $contents);
        }
    }

    /**
     * Recursively processes a directory and populates its contents.
     */
    private function processDir(string $dir, array &$contents): void {
        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue; // Skip current and parent directory references
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                // If it's a directory, recursively process it
                $contents[$path] = [];
                $this->processDir($path, $contents[$path]);
            } elseif (is_file($path)) {
                // If it's a file, add it to the contents and $files array
                $contents[] = $path;
                $this->files[basename($path)] = $path;
            }
        }
    }

    /**
     * Checks if a file exists in the loaded directories.
     */
    public function has(string $filename): bool {
        return isset($this->files[$filename]);
    }

    /**
     * Gets the full path of a file if it exists, or throws an error.
     */
    public function get(string $filename): string {
        if ($this->has($filename)) {
            return $this->files[$filename];
        }

        throw new \Exception("File '{$filename}' not found.");
    }

    /**
     * Static method to get all files in a directory.
     */
    public static function getFilesInDir(string $dir): array {
        if (!is_dir($dir)) {
            throw new \Exception("Directory '{$dir}' does not exist.");
        }

        $files = [];
        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_file($path)) {
                $files[] = $path;
            }
        }

        return $files;
    }
}
$of = new FileLoader(['/var/www/html/App/Views/Templates']);
var_dump($of->get('template.base.html'));