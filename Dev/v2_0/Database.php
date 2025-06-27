<?php

class Database {
    private static ?PDO $instance = null;
    private static array $config = [
        'host' => 'localhost',
        'dbname' => 'your_database',
        'username' => 'your_username',
        'password' => 'your_password'
    ];

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . self::$config['host'] . ';dbname=' . self::$config['dbname'] . ';charset=utf8';
            try {
                self::$instance = new PDO($dsn, self::$config['username'], self::$config['password']);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}