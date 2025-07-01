<?php
class User
{
    public static function findByEmail($email): ?array
    {
        $stmt = Database::getInstance()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function authenticate(string $token): ?array
    {
        $stmt = Database::getInstance()->prepare("SELECT * FROM users WHERE api_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function all(): array
    {
        $stmt = Database::getInstance()->query("SELECT id, name, email, role FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::getInstance()->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['email'], $data['password_hash'], $data['role']]);
        return Database::getInstance()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];
        foreach (['name', 'email', 'password_hash', 'role'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        if (empty($fields)) return false;
        $values[] = $id;
        $stmt = Database::getInstance()->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($values);
    }

    public static function delete(int $id): bool
    {
        $stmt = Database::getInstance()->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
