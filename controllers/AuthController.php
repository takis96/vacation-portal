<?php
class AuthController
{
    //Authenticates a user with email and password, generates a session token, and returns it with the user's role.
    public static function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['email']) || empty($input['password'])) {
            Response::json(['message' => 'Email and password required'], 400);
        }
        $user = User::findByEmail($input['email']);
        if ($user && password_verify($input['password'], $user['password_hash'])) {
            $token = bin2hex(random_bytes(32));
            Database::getInstance()->prepare("UPDATE users SET api_token = ? WHERE id = ?")->execute([$token, $user['id']]);
            Response::json(['token' => $token, 'role' => $user['role']]);
        }
        Response::json(['message' => 'Invalid credentials'], 401);
    }

    //Not really used
    public static function whoami(): void
    {
        $user = AuthController::requireAuth();
        unset($user['password_hash'], $user['api_token']);
        Response::json($user);
    }

    //Validates the Bearer token from the request and returns the authenticated user or a 401 error if invalid.
    public static function requireAuth(): array
    {
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? '';
        if (!preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
            Response::json(['message' => 'Unauthorized'], 401);
        }
        $user = User::authenticate($matches[1]);
        if (!$user) Response::json(['message' => 'Unauthorized'], 401);
        return $user;
    }
}
