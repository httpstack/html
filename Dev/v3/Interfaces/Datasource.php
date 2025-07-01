<?php
namespace Dev\v3\Interfaces;

interface Datasource{

    /**
     * This interface requires that you have a vairable called readOnly
     * that is a boolean. This variable is used to determine if the datasource
     * is in read-only mode or not.
     * 
     * Requires: 
     * @var bool $readOnly
     */
    public function isReadonly(): bool;
    public function setReadOnly(bool $readonly): void;  
}

?>