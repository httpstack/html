<?php
namespace Dev\Concrete;
use Dev\Contracts\Services\CrudProviderInterface;

class UserDatabaseProvider implements CrudProviderInterface
{
    private PDO $db; // PDO connection or similar

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function find(mixed $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): array
    {
        // Build SQL INSERT statement from $data
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO users ($columns) VALUES ($placeholders)");
        $stmt->execute($data);
        $data['id'] = $this->db->lastInsertId(); // Get generated ID
        return $data;
    }

    public function update(mixed $id, array $data): array
    {
        // Build SQL UPDATE statement from $data
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "$key = :$key";
        }
        $setSql = implode(', ', $setParts);
        $data['id'] = $id; // Add ID to data for execution
        $stmt = $this->db->prepare("UPDATE users SET $setSql WHERE id = :id");
        $stmt->execute($data);
        return $this->find($id); // Re-fetch to ensure updated data
    }

    public function delete(mixed $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}

// --- Usage Example ---
// Assuming $pdo is your PDO database connection
// $userProvider = new UserDatabaseProvider($pdo);

// // Create a new user
// $newUser = new User($userProvider);
// $newUser->set('first_name', 'John');
// $newUser->set('last_name', 'Doe');
// $newUser->set('email', 'john.doe@example.com');
// $newUser->save(); // This will call create() on the provider
// echo "New user ID: " . $newUser->get('id') . "\n";

// // Find an existing user
// $existingUser = User::find($userProvider, 1);
// if ($existingUser) {
//     echo "Found user: " . $existingUser->getFullName() . "\n";
//     $existingUser->set('email', 'john.doe.new@example.com');
//     $existingUser->save(); // This will call update() on the provider
//     echo "User email updated.\n";
// }

// // Delete a user
// // $userToDelete = User::find($userProvider, 2);
// // if ($userToDelete) {
// //     $userToDelete->delete();
// //     echo "User deleted.\n";
// // }