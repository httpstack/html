<?php 
namespace Dev\IO\Database\Connection;

use PDO;

/**
 * Interface for PDO database connection.
 */
interface ConnectInterface {
    /**
     * Constructor to initialize the PDO connection.
     *
     * @param string $dsn The Data Source Name, which includes the database type, host, and database name.
     * @param string|null $username The username for the database connection.
     * @param string|null $password The password for the database connection.
     * @param array $options Additional options for the PDO connection.
     */
    public function __construct($dsn, $username = null, $password = null, $options = []);

    /**
     * Get the PDO instance.
     *
     * @return \PDO The PDO instance.
     */
    public function getDbo(): PDO;
    /**
     * Close the PDO connection.
     *
     * This method is optional and can be implemented to close the connection explicitly.
     */
    public function close(): void;
    /**
     * Check if the connection is active.
     *
     * @return bool True if the connection is active, false otherwise.
     */
    public function isConnected(): bool;
    /**
     * Get the last error information.
     *
     * @return array An array containing the error code and message.
     */
    public function getLastError(): array;
    /**
     * Begin a transaction.
     *
     * @return bool True on success, false on failure.
     */

}
?>