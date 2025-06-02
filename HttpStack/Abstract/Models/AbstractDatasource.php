<?php
abstract class AbstractDatasource{
    protected array $model;

    abstract public function getModel():array;
}
?>