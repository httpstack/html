<?php

namespace HttpStack\IO;

use HttpStack\Exceptions\AppException;

/**
 * File Loader.
 *
 * Handles file loading, mapping directories, and finding files
 */
class FileLoader
{
    /**
     * Mapped directories.
     */
    protected array $mappedDirectories = [];

    /**
     * Default extension for files (e.g., for PHP files).
     */
    protected string $defaultExtension = 'php';

    /**
     * Default extension specifically for HTML files, used by readFile.
     */
    protected string $defaultHtmlExtension = 'html';

    /**
     * File cache.
     */
    protected array $fileCache = [];

    /**
     * Create a new file loader.
     */
    public function __construct()
    {
        // Constructor
    }

    /**
     * Map a directory to a namespace.
     *
     * @return $this
     */
    public function mapDirectory(string $name, string $directory): self
    {
        // Ensure the directory exists
        if (!is_dir($directory)) {
            throw new AppException("Directory not found: {$directory}");
        }

        // Store the directory path, ensuring no trailing slash for consistent path concatenation
        $this->mappedDirectories[$name] = rtrim($directory, '/');

        return $this;
    }

    /**
     * Get a mapped directory path.
     */
    public function getDirectory(string $name): ?string
    {
        return $this->mappedDirectories[$name] ?? null;
    }

    /**
     * Get all mapped directories.
     */
    public function getDirectories(): array
    {
        return $this->mappedDirectories;
    }

    /**
     * Find a file by name in mapped directories (searches subdirectories).
     *
     * This method now handles absolute paths and paths with subdirectories relative
     * to the mapped directories.
     *
     * @param string $name The base name or path relative to a mapped directory (e.g., "public/home").
     * @param string|null $directory Optional. The name of a specific mapped directory to search within, or null to search all.
     * @param string|null $extension Optional. The desired file extension (e.g., "html", "php"). If not provided, $this->defaultExtension is used.
     * @return string|null The full path to the found file, or null if not found.
     */
    public function findFile(string $name, ?string $directory = null, ?string $extension = null): ?string
    {
        // echo $name;
        // 1. Handle absolute paths directly
        if (is_file($name) && realpath($name) === $name) {
            return $name;
        }

        // 2. Normalize the search name to use forward slashes for consistency across OS
        $normalizedName = str_replace('\\', '/', trim($name, '/'));

        // 3. Determine the final extension to use. Prioritize provided extension, then defaultExtension.
        $finalExtension = $extension ?? $this->defaultExtension;

        // 4. Check if the provided name already has an extension. If not, append the default one.
        if (!empty($finalExtension) && pathinfo($normalizedName, PATHINFO_EXTENSION) === '') {
            $normalizedName .= '.' . ltrim($finalExtension, '.');
        }

        $searchBaseDirectories = [];

        // Split the normalized name into a directory and a file, for example 'public/home.html' becomes ['public', 'home.html'].
        $parts = explode('/', $normalizedName, 2);

        // This is the new logic to handle your use case.
        if (count($parts) > 1) {
            $subDirectory = $parts[0]; // The folder name, e.g., 'public'
            $fileName = $parts[1]; // The file name, e.g., 'home.html'

            if ($directory !== null && $directory === $subDirectory) {
                // If a specific mapped directory is provided AND it matches the subdirectory in the name,
                // we search within that mapped directory.
                $baseDir = $this->mappedDirectories[$directory] ?? null;
                if ($baseDir && is_dir($baseDir)) {
                    // Construct the final search path by appending the rest of the file path.
                    $fullPath = rtrim($baseDir, '/') . '/' . $fileName;
                    if (is_file($fullPath)) {
                        return $fullPath;
                    }
                }
            } else if ($directory === null) {
                // If no specific mapped directory is provided, we search ALL mapped directories
                // for a folder with the name from the input string (e.g., 'public').
                foreach ($this->mappedDirectories as $baseDir) {
                    $searchPath = rtrim($baseDir, '/') . '/' . $normalizedName;
                    if (is_file($searchPath)) {
                        return $searchPath;
                    }
                }
            }
        } else {
            // 5. If the name is just a file (no slash), proceed with the original logic.
            if ($directory !== null) {
                $baseDir = $this->mappedDirectories[$directory] ?? null;
                if ($baseDir && is_dir($baseDir)) {
                    $searchBaseDirectories[] = $baseDir;
                }
            } else {
                foreach ($this->mappedDirectories as $baseDir) {
                    if (is_dir($baseDir)) {
                        $searchBaseDirectories[] = $baseDir;
                    }
                }
            }

            // 6. Iterate through the determined base directories and check for the file
            foreach ($searchBaseDirectories as $baseDir) {
                $fullPath = rtrim($baseDir, '/') . '/' . $normalizedName;
                if (is_file($fullPath)) {
                    return $fullPath;
                }
            }
        }

        return null; // File not found in any of the specified or mapped directories
    }

    /**
     * Find all files by extension(s) in mapped directories.
     *
     * @param array $extensions An array of extensions (e.g., ['php', 'html']).
     * @param string|null $directory Optional. The name of a specific mapped directory to search within, or null for all mapped directories.
     * @return array An array of full paths to the found files.
     */
    public function findFilesByExtension(array $extensions, ?string $directory = null): array
    {
        $foundFiles = [];
        $searchBaseDirectories = [];

        if ($directory !== null) {
            $baseDir = $this->mappedDirectories[$directory] ?? $directory;
            if (is_dir($baseDir)) {
                $searchBaseDirectories[] = $baseDir;
            }
        } else {
            foreach ($this->mappedDirectories as $baseDir) {
                if (is_dir($baseDir)) {
                    $searchBaseDirectories[] = $baseDir;
                }
            }
        }

        foreach ($searchBaseDirectories as $baseDir) {
            $foundFiles = array_merge($foundFiles, $this->scanDirectoryForExtensions($baseDir, $extensions));
        }

        return array_unique($foundFiles); // Return unique paths
    }


    /**
     * Scan a directory for files with specific extensions recursively.
     *
     * @param string $directory The directory to scan.
     * @param array $extensions An array of extensions (e.g., ['php', 'html']).
     * @return array An array of full paths to the found files.
     */
    protected function scanDirectoryForExtensions(string $directory, array $extensions): array
    {
        $foundFiles = [];
        // Use RecursiveIteratorIterator to traverse directories recursively
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY // Only return files, not directories
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) { // Ensure it's a file
                $fileExtension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
                // Check if the file's extension is in the list of desired extensions
                if (in_array($fileExtension, $extensions)) {
                    $foundFiles[] = $file->getPathname(); // Add the full path
                }
            }
        }
        return $foundFiles;
    }


    /**
     * Load a file's contents.
     *
     * This method now uses file_get_contents() to retrieve the actual content of the file.
     *
     * @param string $path The path or name of the file to load (can be relative to a mapped directory).
     * @param bool $useCache Whether to use or update the file cache.
     * @throws AppException If the file is not found or cannot be read.
     * @return string The content of the file.
     */
    public function loadFile(string $path, bool $useCache = true): string
    {
        // Check the cache first
        if ($useCache && isset($this->fileCache[$path])) {
            return $this->fileCache[$path];
        }

        $foundPath = $path; // Assume the provided path is potentially a direct file path

        // If the provided path is not an existing file, try to find it using the findFile logic
        if (!is_file($path)) {
            // Attempt to resolve the path using findFile, searching all mapped directories
            $foundPath = $this->findFile($path);

            if ($foundPath === null) {
                throw new AppException("File not found: {$path}");
            }
        }

        // Load the file content using file_get_contents
        $content = @file_get_contents($foundPath); // Use @ to suppress warnings; handle errors explicitly below

        if ($content === false) {
            throw new AppException("Failed to read file: {$foundPath}");
        }

        // Cache the content if caching is enabled
        if ($useCache) {
            $this->fileCache[$foundPath] = $content;
            // Also cache by the original requested path if different, for consistent lookup
            if ($path !== $foundPath) {
                $this->fileCache[$path] = $content;
            }
        }

        return $content;
    }

    /**
     * Require a PHP file.
     *
     * @param string $path The path or name of the PHP file to require.
     * @throws AppException If the file is not found.
     * @return mixed The return value of the required file.
     */
    public function requireFile(string $path)
    {
        $foundPath = $path;

        if (!is_file($path)) {
            $foundPath = $this->findFile($path, null, 'php'); // Assume PHP file for require
            if ($foundPath === null) {
                throw new AppException("File not found: {$path}");
            }
        }

        return require $foundPath;
    }

    /**
     * Parse a JSON file and return its content as an associative array.
     *
     * @param string $path The path to the JSON file.
     * @throws AppException If the file is not found or JSON parsing fails.
     * @return array The parsed JSON data.
     */
    public function parseJsonFile(string $path): array
    {
        if (is_file($path)) {
            $content = file_get_contents($path);
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new AppException("Failed to parse JSON file: {$path}. Error: " . json_last_error_msg());
            }

            return $data;
        } else {
            throw new AppException("File not found: {$path}");
        }
    }

    /**
     * Include a PHP file.
     *
     * @param string $path The path or name of the PHP file to include.
     * @throws AppException If the file is not found.
     * @return mixed The return value of the included file.
     */
    public function includeFile(string $path)
    {
        $foundPath = $path;

        if (!is_file($path)) {
            $foundPath = $this->findFile($path, null, 'php'); // Assume PHP file for include
            if ($foundPath === null) {
                throw new AppException("File not found: {$path}");
            }
        }

        return include $foundPath;
    }

    /**
     * Write content to a file.
     *
     * @param string $path The full path to the file to write.
     * @param string $content The content to write to the file.
     * @return bool True on success, false on failure.
     */
    public function writeFile(string $path, string $content): bool
    {
        // Ensure the directory exists; create it recursively if it doesn't
        $directory = dirname($path);
        if (!is_dir($directory)) {
            // mkdir with recursive flag and default permissions
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                // Check again if directory exists in case of race condition
                throw new AppException("Failed to create directory: {$directory}");
            }
        }

        // Write the content to the file
        $result = file_put_contents($path, $content);

        // Update the cache if write was successful
        if ($result !== false) {
            $this->fileCache[$path] = $content;
            return true;
        }

        return false; // Write operation failed
    }

    /**
     * Read the content of a file, specifically looking for HTML files by default.
     *
     * @param string $baseName The base name or path relative to a mapped directory (e.g., "public/home").
     * @throws AppException If the file is not found.
     * @return string The content of the file.
     */
    public function readFile(string $baseName): string
    {
        // Use findFile to locate the file, explicitly looking for the default HTML extension
        $path = $this->findFile($baseName, null, $this->defaultHtmlExtension);
        if ($path === null) {
            throw new AppException("File not found: {$baseName}");
        }
        return file_get_contents($path);
    }

    /**
     * Set the default file extension for files found by findFile when no extension is specified.
     *
     * @param string $extension The new default extension (e.g., "php", "txt").
     * @return $this
     */
    public function setDefaultExtension(string $extension): self
    {
        $this->defaultExtension = ltrim($extension, '.');
        return $this;
    }

    /**
     * Get the current default file extension.
     */
    public function getDefaultExtension(): string
    {
        return $this->defaultExtension;
    }

    /**
     * Clear the file cache.
     *
     * @return $this
     */
    public function clearCache(): self
    {
        $this->fileCache = [];
        return $this;
    }

    /**
     * Handle duplicate files found, based on a specified strategy.
     *
     * @param array $files An array of file paths that may contain duplicates.
     * @param string $strategy The strategy to use: 'first', 'last', or 'all'.
     * @return array An array of file paths after applying the duplicate handling strategy.
     */
    public function handleDuplicates(array $files, string $strategy = 'first'): array
    {
        $result = [];
        $fileNames = [];

        // Group files by their base name
        foreach ($files as $file) {
            $name = basename($file);
            if (!isset($fileNames[$name])) {
                $fileNames[$name] = [];
            }
            $fileNames[$name][] = $file;
        }

        // Apply the chosen strategy for duplicates
        foreach ($fileNames as $name => $paths) {
            if (count($paths) === 1) {
                // If only one file with this name, add it directly
                $result[] = $paths[0];
            } else {
                // Handle duplicates based on strategy
                if ($strategy === 'first') {
                    $result[] = $paths[0]; // Take the first one found
                } elseif ($strategy === 'last') {
                    $result[] = end($paths); // Take the last one found
                } elseif ($strategy === 'all') {
                    $result = array_merge($result, $paths); // Include all duplicates
                }
            }
        }

        return $result;
    }
}
