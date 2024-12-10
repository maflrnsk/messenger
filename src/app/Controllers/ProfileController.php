<?php
namespace App\Controllers;

use App\Models\User;

class ProfileController {
    public function showProfile() {
        session_start();

        // Проверка, залогинен ли пользователь
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit;
        }

        // Получаем информацию о пользователе из сессии
        $user = $_SESSION['user'];

        // Подключение к базе данных
        $db = require __DIR__ . '/../config/db.php'; // подключение к БД
        $userModel = new User($db); // Создаем экземпляр модели User с передачей подключения

        // Включаем представление профиля и передаем данные о пользователе
        include __DIR__ . '/../Views/profile.php';
    }

    public function logout() {
        session_start();
        session_destroy(); // Уничтожаем сессию
        header("Location: /login"); // Перенаправляем на страницу входа
        exit;
    }

    public function updateProfile() {
        session_start();
        
        if (!isset($_SESSION['user'])) {
            echo json_encode(['error' => 'Вы не авторизованы']);
            exit;
        }
    
        $user = $_SESSION['user'];
        $db = require __DIR__ . '/../config/db.php';
        $userModel = new User($db);
    
        $error_message = '';
        $response = ['success' => false];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $profile_picture = $_FILES['profile_picture'] ?? null;
    
            // Обновление имени пользователя
            if (!empty($username)) {
                $userModel->updateUsername($user['id'], $username);
                $_SESSION['user']['username'] = $username;  // Обновляем имя в сессии
                $response['username'] = $username;
            }
    
            // Обновление пароля
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $userModel->updatePassword($user['id'], $hashed_password);
                $_SESSION['user']['password'] = $hashed_password;  // Обновляем пароль в сессии
            }
    
            // Обновление фото профиля
            if ($profile_picture && $profile_picture['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
    
                $uploaded_file = $upload_dir . basename($profile_picture['name']);
    
                if (move_uploaded_file($profile_picture['tmp_name'], $uploaded_file)) {
                    $userModel->updateProfilePicture($user['id'], $uploaded_file);
                    $_SESSION['user']['profile_picture'] = $uploaded_file;
                    $response['profile_picture'] = basename($uploaded_file);  // Название нового файла
                } else {
                    $error_message = "Ошибка при загрузке фото!";
                }
            }
    
            // Сообщение об успехе или ошибке
            if (empty($error_message)) {
                $response['success'] = true;
                $response['message'] = 'Профиль успешно обновлен!';
            } else {
                $response['error'] = $error_message;
            }
        }
    
        // Отправка JSON-ответа
        header('Content-Type: application/json'); // Устанавливаем заголовок для корректной обработки
        echo json_encode($response);  // Отправляем ответ в формате JSON
        exit;
    }    
}

