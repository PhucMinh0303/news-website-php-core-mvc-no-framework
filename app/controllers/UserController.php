<?php

namespace App\Controllers;

use \UserModel;

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->getUsers();

        // Trả về view hoặc JSON
        header('Content-Type: application/json');
        echo json_encode($users);
    }

    public function show($id)
    {
        $user = $this->userModel->getUser($id);

        if ($user) {
            header('Content-Type: application/json');
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
        }
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($data && isset($data['name'], $data['email'])) {
            $id = $this->userModel->create($data['name'], $data['email']);

            if ($id) {
                http_response_code(201);
                echo json_encode([
                    'message' => 'User created successfully',
                    'id' => $id
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create user']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
        }
    }
}
