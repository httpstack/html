<?php
namespace App\IO;




class FileLoader {
    private $dirs = [];

    /**
     * Load directories from an array or a single directory.
     *
     * @param array|string $input Directories to load.
     */
    public function load($input) {
        if (is_string($input)) {
            $input = [$input];
        }

        foreach ($input as $dir) {
            $this->addDirectory($dir);
        }
    }

    /**
     * Add a directory to the internal structure.
     *
     * @param string $dir Directory path to add.
     * @throws \Exception if the directory is a subdirectory of an existing directory.
     */
    private function addDirectory($dir) {
        // Normalize the directory path
        $dir = rtrim(realpath($dir), '/') . '/';

        if ($dir === false) {
            throw new \Exception("Directory '$dir' does not exist.");
        }

        // Check for subdirectory conflicts
        foreach ($this->dirs as $existingDir => $subDirs) {
            if (strpos($dir, $existingDir) === 0) {
                throw new \Exception("Cannot add directory '$dir' as it is a subdirectory of '$existingDir'");
            }
        }

        // Initialize the directory structure
        if (!isset($this->dirs[$dir])) {
            $this->dirs[$dir] = $this->scanDirectory($dir);
        }
    }

    /**
     * Scan a directory and return its structure.
     *
     * @param string $dir Directory to scan.
     * @return array Array of files and subdirectories.
     */
    private function scanDirectory($dir) {
        $structure = [];

        // Scan the directory
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue; // Skip current and parent directory links
            }

            $path = $dir . $item;
            if (is_dir($path)) {
                $structure[$item] = $this->scanDirectory($path); // Recursively scan subdirectories
            } else {
                $structure[] = $item; // Add file
            }
        }

        return $structure;
    }

    /**
     * Search for a file in the loaded directories.
     *
     * @param string $filename Name of the file to search for.
     * @return string|null Full path to the file if found, null otherwise.
     */
    public function findFile($filename) {
        foreach ($this->dirs as $dir => $subDirs) {
            $result = $this->searchFileInDir($filename, $dir);
            if ($result !== null) {
                return $result; // Return the found file path
            }
        }
        return null; // File not found
    }

    /**
     * Recursively search for a file in a directory.
     *
     * @param string $filename Name of the file to search for.
     * @param string $dir Directory to search in.
     * @return string|null Full path to the file if found, null otherwise.
     */
    private function searchFileInDir($filename, $dir) {
        foreach ($this->dirs[$dir] as $item) {
            if (is_array($item)) {
                // Recursively search in subdirectories
                $subDir = $dir . key($item) . '/';
                $result = $this->searchFileInDir($filename, $subDir);
                if ($result !== null) {
                    return $result; // Return found file path
                }
            } elseif ($item === $filename) {
                return $dir . $filename; // Found the file
            }
        }
        return null; // File not found in this directory
    }
}

$fileLoader = new \App\IO\FileLoader();

try {
    // Load directories
    $fileLoader->load(['/var/www/html/public/assets', '/var/www/html/App/Views']);

    // Find a file
    $filePath = $fileLoader->findFile('style.css');

    if ($filePath !== null) {
        echo "File found at: $filePath";
    } else {
        echo "File not found.";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}