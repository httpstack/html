<?php 
namespace Dev\IO\Database\Collections;

class ActiveTable{
    protected $pdo;
    protected $tableName;

    public function __construct($pdo, $tableName) {

        $this->pdo = $pdo;
        $this->tableName = $tableName;
    }

    public function getAllRecords() {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->tableName}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecordById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->tableName} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function insertRecord($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $stmt = $this->pdo->prepare("INSERT INTO {$this->tableName} ({$columns}) VALUES ({$placeholders})");
        return $stmt->execute($data);
    }

    public function updateRecord($id, $data) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = :{$key}";
        }
        $setString = implode(', ', $set);
        $stmt = $this->pdo->prepare("UPDATE {$this->tableName} SET {$setString} WHERE id = :id");
        $data[':id'] = $id;
        return $stmt->execute($data);
    }

    public function deleteRecord($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->tableName} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>