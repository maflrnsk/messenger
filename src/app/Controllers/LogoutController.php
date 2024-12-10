<?php
namespace App\Controllers;

class LogoutController {
    public function logout() {
        // Стартуем сессию и уничтожаем ее
        session_start();
        session_destroy();
        
        // Перенаправляем пользователя на страницу логина
        header("Location: /login");
        exit;
    }
}
