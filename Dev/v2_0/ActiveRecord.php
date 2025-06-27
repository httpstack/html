<?php

abstract class ActiveRecord {
    protected static string $tableName;
    protected array $data = [];
    private array $dirtyAttributes = [];

    public function __construct(array $data = []) {
        $this->data = $data;
    }

    public function __set(string $name, $value): void {
        if (array_key_exists($name, $this->data) && $this->data[$name] === $value) {
            return;
        }
        $this->data[$name] = $value;
        $this->dirtyAttributes[$name] = true;
    }

    public function __get(string $name) {
        return $this->data[$name] ?? null;
    }

    public static function find(int $id): ?static {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM ' . static::$tableName . ' WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new static($data);
        }
        return null;
    }

    public static function findAll(array $conditions = []): array {
        $pdo = Database::getInstance();
        $sql = 'SELECT * FROM ' . static::$tableName;
        $params = [];

        if (!empty($conditions)) {
            $sql .= ' WHERE ';
            $clause = [];
            foreach ($conditions as $key => $value) {
                $clause[] = "$key = :$key";
                $params[":$key"] = $value;
            }
            $sql .= implode(' AND ', $clause);
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new static($row);
        }
        return $results;
    }

    public function save(): bool {
        if (isset($this->data['id']) && !empty($this->data['id'])) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    private function create(): bool {
        $pdo = Database::getInstance();
        $columns = array_keys($this->dirtyAttributes);
        $placeholders = array_map(fn($col) => ":$col", $columns);

        $sql = 'INSERT INTO ' . static::$tableName . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';

        $stmt = $pdo->prepare($sql);
        foreach ($columns as $column) {
            $stmt->bindValue(":$column", $this->data[$column]);
        }

        $result = $stmt->execute();
        if ($result) {
            $this->data['id'] = (int)$pdo->lastInsertId();
            $this->dirtyAttributes = [];
        }
        return $result;
    }

    private function update(): bool {
        if (empty($this->dirtyAttributes)) {
            return true; // Nothing to update
        }
        $pdo = Database::getInstance();
        $columns = array_keys($this->dirtyAttributes);
        $setClause = array_map(fn($col) => "$col = :$col", $columns);

        $sql = 'UPDATE ' . static::$tableName . ' SET ' . implode(', ', $setClause) . ' WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        foreach ($columns as $column) {
            $stmt->bindValue(":$column", $this->data[$column]);
        }
        $stmt->bindValue(':id', $this->data['id']);

        $result = $stmt->execute();
        if ($result) {
            $this->dirtyAttributes = [];
        }
        return $result;
    }

    public function delete(): bool {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('DELETE FROM ' . static::$tableName . ' WHERE id = :id');
        return $stmt->execute([':id' => $this->data['id']]);
    }
}