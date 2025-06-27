<?php

/**
 * An abstract class that provides a base for ActiveRecord-style models.
 *
 * This class encapsulates the connection to a database and provides a set of
 * methods for performing common CRUD (Create, Read, Update, Delete) operations.
 * By extending this class, your models will inherit this functionality,
 * allowing you to work with your database tables in an object-oriented way.
 *
 * To use this, you would create a model class that extends ActiveRecord, like so:
 *
 * class User extends ActiveRecord {
 * protected static $tableName = 'users';
 * protected static $columns = ['id', 'username', 'email', 'password'];
 * }
 *
 * $user = new User(['username' => 'johndoe', 'email' => 'john@example.com']);
 * $user->save();
 */
abstract class ActiveRecord
{
    /**
     * The database connection object.
     *
     * @var PDO
     */
    protected static $database;

    /**
     * The name of the database table.
     * This must be defined in the child class.
     *
     * @var string
     */
    protected static $tableName = '';

    /**
     * The columns in the database table.
     * This must be defined in the child class.
     *
     * @var array
     */
    protected static $columns = [];

    /**
     * An array of errors from the last operation.
     *
     * @var array
     */
    public $errors = [];

    /**
     * Sets the database connection for all ActiveRecord models.
     *
     * @param PDO $database The PDO database connection object.
     * @return void
     */
    public static function setDatabase(PDO $database)
    {
        self::$database = $database;
    }

    /**
     * Finds a single record by its ID.
     *
     * @param int $id The ID of the record to find.
     * @return static|false An instance of the calling class or false if not found.
     */
    public static function find($id)
    {
        $sql = "SELECT * FROM " . static::$tableName . " WHERE id = :id";
        $stmt = self::$database->prepare($sql);
        $stmt->execute(['id' => $id]);
        $object_array = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$object_array) {
            return false;
        }
        return new static($object_array);
    }

    /**
     * Finds all records in the table.
     *
     * @return static[] An array of objects of the calling class.
     */
    public static function findAll()
    {
        $sql = "SELECT * FROM " . static::$tableName;
        $stmt = self::$database->query($sql);
        $object_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $objects = [];
        foreach ($object_array as $row) {
            $objects[] = new static($row);
        }
        return $objects;
    }

    /**
     * Saves the current record to the database.
     *
     * This method will either INSERT a new record or UPDATE an existing one,
     * depending on whether the 'id' property is set.
     *
     * @return bool True on success, false on failure.
     */
    public function save()
    {
        if (isset($this->id)) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    /**
     * Deletes the current record from the database.
     *
     * @return bool True on success, false on failure.
     */
    public function delete()
    {
        $sql = "DELETE FROM " . static::$tableName . " WHERE id = :id LIMIT 1";
        $stmt = self::$database->prepare($sql);
        return $stmt->execute(['id' => $this->id]);
    }

    /**
     * The constructor.
     *
     * @param array $args An associative array of properties to set on the object.
     */
    public function __construct($args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Gets the attributes of the object based on the defined columns.
     *
     * @return array An associative array of the object's properties.
     */
    public function attributes()
    {
        $attributes = [];
        foreach (static::$columns as $column) {
            if ($column == 'id') {
                continue;
            }
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }

    /**
     * Creates a new record in the database.
     *
     * @return bool True on success, false on failure.
     */
    protected function create()
    {
        $attributes = $this->attributes();
        $sql = "INSERT INTO " . static::$tableName . " (";
        $sql .= join(', ', array_keys($attributes));
        $sql .= ") VALUES (:";
        $sql .= join(', :', array_keys($attributes));
        $sql .= ")";

        $stmt = self::$database->prepare($sql);
        $stmt->execute($attributes);

        if ($stmt->rowCount() > 0) {
            $this->id = self::$database->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Updates an existing record in the database.
     *
     * @return bool True on success, false on failure.
     */
    protected function update()
    {
        $attributes = $this->attributes();
        $attribute_pairs = [];
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}=:{$key}";
        }

        $sql = "UPDATE " . static::$tableName . " SET ";
        $sql .= join(', ', $attribute_pairs);
        $sql .= " WHERE id = :id";

        $stmt = self::$database->prepare($sql);
        $attributes['id'] = $this->id;
        return $stmt->execute($attributes);
    }
}