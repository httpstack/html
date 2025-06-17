<?php 
namespace Dev\IO\DataBase\Connection;
use Dev\IO\Database\Connection\ConnectInterface;
use PDO;
use PDOException;
use RuntimeException;
class PdoConnect implements ConnectInterface {
    private $pdo;

    public function __construct($dsn, $username = null, $password = null, $options = []) {
        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }

    public function getDbo(): PDO {
        if ($this->pdo === null) {
            throw new RuntimeException("No database connection established.");
        }
        // Optionally, you can check if the connection is still active
        if (!$this->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)) {
            throw new RuntimeException("Database connection is not active.");
        }
        // Return the PDO instance
        return $this->pdo;
    }
    public function close():void {
        $this->pdo = null;
    }
    public function isConnected(): bool {
        return $this->pdo !== null;
    }
    public function getLastError(): array {
        if ($this->pdo) {
            $errorInfo = $this->pdo->errorInfo();
            return [
                'code' => $errorInfo[0],
                'message' => $errorInfo[2]
            ];
        }
        return ['code' => null, 'message' => 'No connection established'];
    }
}
?>