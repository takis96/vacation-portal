<?php
class Vacation
{
    public static function allForUser(int $userId): array
    {
        $stmt = Database::getInstance()->prepare("SELECT * FROM vacations WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data): int
    {
        $stmt = Database::getInstance()->prepare("INSERT INTO vacations (user_id, date_from, date_to, reason) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['user_id'], $data['date_from'], $data['date_to'], $data['reason']]);
        return Database::getInstance()->lastInsertId();
    }
}
