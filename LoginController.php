<?php
// В файле app/Controllers/LoginController.php
namespace App\Controllers;

use App\Models\User;

class LoginController {
    public function showLoginForm() {
        include __DIR__ . '/../Views/login.php';
    }

    public function login() {
        session_start();

        $error_message = '';
        $db = require __DIR__ . '/../config/db.php'; // Подключаем БД
        $userModel = new User($db); // Создаём экземпляр модели User

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Используем findByEmailOrUsername для поиска пользователя по email
            $user = $userModel->findByEmailOrUsername($email, $email);

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    // Сохраняем информацию о пользователе в сессии
                    $_SESSION['user'] = $user;
                    header("Location: /profile");
                    exit;
                } else {
                    $error_message = "Неверный пароль";
                }
            } else {
                $error_message = "Пользователь не найден";
            }
        }

        include __DIR__ . '/../Views/login.php';
    }
}

