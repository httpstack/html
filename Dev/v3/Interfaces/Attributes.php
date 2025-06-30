<?php
namespace Dev\v3\Interfaces;

interface Attributes{
    /**
     * You will needd to create an array to store the attributes into
     * The name of the variable is imatterial because thje signature cant say 
     * either way
     */
    /**
     * 
     * Retrieves the value associated with the specified key.
     *
     * @param string $key The key to retrieve the value for.
     * @return mixed The value associated with the key.
     */
    public function get(string $key):mixed;
    /**
     * Retrieves all key-value pairs.
     *
     * @return mixed An associative array of all key-value pairs.
     */ 
    public function getAll():mixed;  
    /**
     * Sets the value for the specified key.
     *
     * @param string $key The key to set the value for.
     * @param mixed $value The value to set.
     * @return void
     */   
    public function set(string $key, mixed $value):void;
    /**
     * Sets multiple key-value pairs.
     *
     * @param array $data An associative array of key-value pairs to set.
     * @return void
     */
    public function setAll(array $data):void;
    /**
     * Removes the value associated with the specified key.
     *
     * @param string $key The key to remove.
     * @return void
     */
    public function remove(string $key):void;
    /**
     * Clears all key-value pairs.
     *
     * @return void
     */
    public function clear():void;
    /**
     * Checks if a key exists.
     *
     * @param string $key The key to check for existence.
     * @return bool True if the key exists, false otherwise.
     */
    public function has(string $key):bool;

}

?>