<?php 
namespace Dev\IO\Database\Items;

class ActiveRecord {
    protected $table;
    protected $primaryKey = 'id';
    protected $attributes = [];
    protected $pdo;

    public function __construct($pdo, $table, $id) {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->id = $id;
        $this->getRecord();
    }
    protected function getRecord() {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->execute([':id' => $this->id]);
        $record = $stmt->fetch();
        if ($record) {
            $this->attributes = $record;
        } else {
            throw new \Exception("Record not found in table {$this->table} with ID {$this->id}");
        }
    }
    public function update($data) {
        $set = [];
        $params = [':id' => $this->id];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }
        $setString = implode(', ', $set);
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET {$setString} WHERE {$this->primaryKey} = :id");
        return $stmt->execute($params);
    }
    public function delete() {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute([':id' => $this->id]);
    }
    public function setAttribute($key, $value) {
        $this->attributes[$key] = $value;
    }
    public function getAttribute($key) {
        return $this->attributes[$key] ?? null;
    }
    public function getAttributes() {
        return $this->attributes;
    }
    public function getTable() {
        return $this->table;
    }
    public function getPrimaryKey() {
        return $this->primaryKey;
    }


    public function save() {
        // Implement save logic here
    }
}
?>