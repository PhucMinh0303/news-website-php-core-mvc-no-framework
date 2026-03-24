<?php
// config/database.php
return [
    'host' => 'localhost',
    'dbname' => 'capitalam2',
    'username' => 'capitalam21',
    'password' => '123456789',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];