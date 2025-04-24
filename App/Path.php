<?php
namespace App;

class Path{
    private string $path;
    private string $basePath;
    private string $fullPath;
    private string $fileName;
    private string $extension;
    private array $segments = [];
    private array $queryParams = [];
    
    public function __construct(string $path, string $basePath = '') {
        $this->basePath = rtrim($basePath, '/');
        $this->path = ltrim($path, '/');
        $this->fullPath = rtrim($this->basePath . '/' . $this->path, '/');
        $this->segments = explode('/', trim($this->path, '/'));
        $this->fileName = basename($this->fullPath);
        $this->extension = pathinfo($this->fullPath, PATHINFO_EXTENSION);
    }
    public function getPath(): string {
        return $this->path;
    }
    public function getBasePath(): string {
        return $this->basePath;
    }
    public function getFullPath(): string {
        return $this->fullPath;
    }
    public function getFileName(): string {
        return $this->fileName;
    }
    public function getExtension(): string {
        return $this->extension;
    }
    public function getSegments(): array {
        return $this->segments;
    }
    public function getQueryParams(): array {
        return $this->queryParams;
    }
    public function setQueryParams(array $queryParams): void {
        $this->queryParams = $queryParams;
    }
    public function addQueryParam(string $key, string $value): void {
        $this->queryParams[$key] = $value;
    }
    public function removeQueryParam(string $key): void {
        unset($this->queryParams[$key]);
    }
    public function hasQueryParam(string $key): bool {
        return isset($this->queryParams[$key]);
    }
    public function getQueryParam(string $key): ?string {
        return $this->queryParams[$key] ?? null;
    }
    public function __toString(): string {
        return $this->fullPath;
    }
    public function __get(string $name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }
    public function __set(string $name, $value): void {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }
    public function __isset(string $name): bool {
        return isset($this->$name);
    }
    public function __unset(string $name): void {
        if (property_exists($this, $name)) {
            unset($this->$name);
        }
    }
}

$p = new Path('/path/to/file.txt?key=valiue&key2=value2', '/base/path');
echo $p->getPath(); // Output: path/to/file.txt
echo $p->getBasePath(); // Output: (empty string)
echo $p->getFullPath(); // Output: /path/to/file.txt
echo $p->getFileName(); // Output: file.txt
echo $p->getExtension(); // Output: txt
echo implode(', ', $p->getSegments()); // Output: path, to, file.txt
echo $p->getQueryParam('key'); // Output: (null)
// echo $p->getQueryParams(); // Output: (empty array)