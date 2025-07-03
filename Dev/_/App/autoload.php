<?php 
define("DOC_ROOT", "/var/www/html");
define("APP_ROOT", DOC_ROOT . "/Dev/_");
function normalize_path($path) {
    $isAbsolute = ($path[0] === '/');
    $segments = explode('/', $path);
    $parts = [];
    foreach ($segments as $segment) {
        if ($segment === '' || $segment === '.') {
            continue;
        }
        if ($segment === '..') {
            if (!empty($parts)) {
                array_pop($parts);
            }
        } else {
            $parts[] = $segment;
        }
    }
    $normalized = ($isAbsolute ? '/' : '') . implode('/', $parts);
    return $normalized;
}
spl_autoload_register(function($className){
    $file = APP_ROOT . "/" . str_replace('\\', '/', $className) . '.php';
    $file = normalize_path($file);
    if (file_exists($file)) {
        require_once $file;
    }
});

?>