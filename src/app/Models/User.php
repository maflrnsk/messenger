<?php
namespace App\Models;

use mysqli;

class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findByEmailOrUsername($email, $username)
    {
        $sql = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function findById($id)
    {
        $sql = "SELECT id, username, email, profile_picture FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateUsername($id, $username)
    {
        $sql = "UPDATE users SET username = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();
    }

    public function updatePassword($id, $password)
    {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $password, $id);
        $stmt->execute();
    }

    public function updateProfilePicture($id, $profile_picture)
    {
        $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $profile_picture, $id);
        $stmt->execute();
    }

    // Метод для создания нового пользователя
    public function create($email, $password, $username)
    {
        // Подготовка SQL-запроса для вставки нового пользователя
        $sql = "INSERT INTO users (email, password, username) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $email, $password, $username);
        
        // Выполнение запроса
        return $stmt->execute();
    }

    // Метод для получения всех пользователей
    public function getAllUsers()
    {
        // Исправляем использование $this->conn вместо $this->db
        $query = "SELECT id, username, email FROM users";  // Извлекаем id и имя пользователя (или email)
        $result = $this->conn->query($query);  // Используем $this->conn

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        return $users;  // Возвращаем массив пользователей
    }
}

