<?php
class VacationController
{
    //Returs all vacation requests for the authenticated employee as JSON
    public static function index(): void
    {
        $user = AuthController::requireAuth();
        if ($user['role'] !== 'employee') Response::json(['message' => 'Forbidden'], 403);
        Response::json(Vacation::allForUser($user['id']));
    }

    //Creates a new vacation request for the authenticated employee
    public static function store(): void
    {
        $user = AuthController::requireAuth();
        if ($user['role'] !== 'employee') Response::json(['message' => 'Forbidden'], 403);

        $input = json_decode(file_get_contents('php://input'), true);
        foreach (['date_from', 'date_to', 'reason'] as $field) {
            if (empty($input[$field])) Response::json(['message' => "Missing $field"], 400);
        }
        $input['user_id'] = $user['id'];
        $id = Vacation::create($input);
        Response::json(['id' => $id], 201);
    }
}
