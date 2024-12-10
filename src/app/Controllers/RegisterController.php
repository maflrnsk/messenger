<?php
namespace App\Controllers;

use App\Models\User;

class RegisterController
{
    public function showRegisterForm()
    {
        include __DIR__ . '/../Views/register.php';
    }

    public function register()
    {
        session_start();

        $error_message = '';
        $success_message = '';

        // Подключение к базе данных
        $db = require __DIR__ . '/../config/db.php';  // Подключение конфигурации базы данных

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $username = $_POST['username'];

            // Проверка корректности email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_message = "Некорректный email!";
            } elseif (empty($username) || !preg_match('/^[a-zA-Z0-9а-яА-Я_-]{3,30}$/u', $username)) {
                // Проверка корректности имени пользователя
                $error_message = "Некорректное имя пользователя! Оно должно быть длиной от 3 до 30 символов и содержать только буквы, цифры, подчеркивания или дефисы.";
            } else {
                // Передаем объект подключения к базе данных в модель User
                $userModel = new User($db);  // Передаем подключение в конструктор

                $existingUser = $userModel->findByEmailOrUsername($email, $username);

                if ($existingUser) {
                    $error_message = "Пользователь с таким email или именем пользователя уже существует!";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $userModel->create($email, $hashed_password, $username);
                    $_SESSION['success_message'] = "Регистрация успешна! Теперь вы можете войти.";
                    header("Location: /login");
                    exit;
                }
            }
        }

        // Отображение формы с ошибками и сообщениями
        include __DIR__ . '/../Views/register.php';
    }
}

