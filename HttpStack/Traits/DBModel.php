<?php
namespace HttpStack\Traits;
use HttpStack\DataBase\DBConnect;
trait DBModel{
    public function setDB(DBConnect $dbConnect){
        $this->dbConnect = $dbConnect;
    }
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->dbConnect->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
?>