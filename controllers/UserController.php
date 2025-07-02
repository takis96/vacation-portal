<?php
class UserController
{
    //Returns a list of all users as JSON
    public static function index(): void
    {
        $user = AuthController::requireAuth();
        if ($user['role'] !== 'manager') Response::json(['message' => 'Forbidden'], 403);
        Response::json(User::all());
    }

    //Returns details of a specific user by id
    public static function show($id): void
    {
        $user = AuthController::requireAuth();
        if ($user['role'] !== 'manager') Response::json(['message' => 'Forbidden'], 403);
        $found = User::find((int)$id);
        $found ? Response::json($found) : Response::json(['message' => 'User not found'], 404);
    }

    //Creates a new user with all the neccessary values
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

    ////Updates a user with the values given
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

    //Deletes a user by id
    public static function destroy($id): void
    {
        $user = AuthController::requireAuth();
        if ($user['role'] !== 'manager') Response::json(['message' => 'Forbidden'], 403);
        User::delete((int)$id) ? Response::json(['message' => 'User deleted']) : Response::json(['message' => 'Not found'], 404);
    }
}
