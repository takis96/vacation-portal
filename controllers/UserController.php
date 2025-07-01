<?php
class UserController
{
    public static function index(): void
    {
        $user = AuthController::requireAuth();
        if ($user['role'] !== 'manager') Response::json(['message' => 'Forbidden'], 403);
        Response::json(User::all());
    }

    public static function show($id): void
    {
        $user = AuthController::requireAuth();
        if ($user['role'] !== 'manager') Response::json(['message' => 'Forbidden'], 403);
        $found = User::find((int)$id);
        $found ? Response::json($found) : Response::json(['message' => 'User not found'], 404);
    }

    public static function store(): void
    {
        $user = AuthController::requireAuth();
        if ($user['role'] !== 'manager') Response::json(['message' => 'Forbidden'], 403);

        $input = json_decode(file_get_contents('php://input'), true);
        foreach (['name', 'email', 'password', 'role'] as $field) {
            if (empty($input[$field])) Response::json(['message' => "Missing $field"], 400);
        }
        $input['password_hash'] = password_hash($input['password'], PASSWORD_DEFAULT);
        unset($input['password']);
        try {
            $id = User::create($input);
            Response::json(['id' => $id], 201);
        } catch (PDOException $e) {
            Response::json(['message' => 'Email already exists'], 400);
        }
    }

    public static function update($id): void
    {
        $user = AuthController::requireAuth();
        if ($user['role'] !== 'manager') Response::json(['message' => 'Forbidden'], 403);

        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['password'])) {
            $input['password_hash'] = password_hash($input['password'], PASSWORD_DEFAULT);
            unset($input['password']);
        }
        if (User::update((int)$id, $input)) {
            Response::json(['message' => 'User updated']);
        } else {
            Response::json(['message' => 'No data updated'], 400);
        }
    }

    public static function destroy($id): void
    {
        $user = AuthController::requireAuth();
        if ($user['role'] !== 'manager') Response::json(['message' => 'Forbidden'], 403);
        User::delete((int)$id) ? Response::json(['message' => 'User deleted']) : Response::json(['message' => 'Not found'], 404);
    }
}
