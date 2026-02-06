<?php
namespace App\Controllers;

use App\Models\User;

class UserController
{
    private $userModel;
    
    public function __construct()
    {
        $this->userModel = new User();
    }
    
    public function index()
    {
        $users = $this->userModel->all();
        
        // Trả về view hoặc JSON
        header('Content-Type: application/json');
        echo json_encode($users);
    }
    
    public function show($id)
    {
        $user = $this->userModel->find($id);
        
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
        
        if ($data) {
            $id = $this->userModel->create($data);
            
            http_response_code(201);
            echo json_encode([
                'message' => 'User created successfully',
                'id' => $id
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
        }
    }
}