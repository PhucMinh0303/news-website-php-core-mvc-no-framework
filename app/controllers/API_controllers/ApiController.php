<?php
namespace App\Controllers\Api;

class ApiController
{
    protected function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
    
    protected function success($data = null, $message = 'Success', $statusCode = 200)
    {
        return $this->jsonResponse([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
    
    protected function error($message = 'Error', $errors = [], $statusCode = 400)
    {
        return $this->jsonResponse([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
    
    protected function validateRequired($data, $requiredFields)
    {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = "The $field field is required.";
            }
        }
        
        if (!empty($errors)) {
            $this->error('Validation failed', $errors, 422);
        }
    }
    
    protected function getInput()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $input = $_POST;
        }
        
        return $input ?? [];
    }
}