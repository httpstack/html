<?php
namespace Dev\Template\Contracts;

use HttpStack\App\Models\TemplateModel;


interface TemplateInterface
{

    /**
     * Set a variable in the templates data array.
     * Requires: @var array $data;
     */
    public function setVariables(array $variables): void;
    public function render(): string;
    public function load(string $templatePath): void;
    public function has(string $key): bool;
    public function get(string $key): mixed;
    public function set(string $key, mixed $value): void;
}

?>