<?php
namespace App\Controllers;

use App\Models\Message;
use App\Models\User;

class MessengerController
{
    private $messageModel;
    private $userModel;
    private $db;

    public function __construct()
    {
        // Загружаем подключение к базе данных из db.php
        $this->db = require __DIR__ . '/../config/db.php';  // Это теперь объект mysqli

        // Проверка на ошибки подключения
        if ($this->db->connect_error) {
            die("Ошибка подключения: " . $this->db->connect_error);
        }

        // Передаем объект $this->db в конструктор модели Message
        $this->messageModel = new Message($this->db);
        $this->userModel = new User($this->db);
    }

    public function index()
    {
        session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $selected_user_id = $_GET['user'] ?? null;

        $users = $this->userModel->getAllUsers();
        
        $messages = [];
        if ($selected_user_id) {
            $messages = $this->messageModel->getMessages($user_id, $selected_user_id);
        }

        include __DIR__ . '/../Views/messenger/index.php';
    }

    public function sendMessage()
    {
        session_start();
        if (!isset($_SESSION['user'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $selected_user_id = $_GET['user'] ?? null;
        $message_content = $_POST['message'] ?? '';
        $photo_path = null;

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/photos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $photo_name = uniqid() . '_' . basename($_FILES['photo']['name']);
            $photo_path = $upload_dir . $photo_name;

            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                $photo_path = null;
            }
        }

        $this->messageModel->sendMessage($user_id, $selected_user_id, $message_content, $photo_path);
        exit;
    }

    public function showMessages($selected_user_id)
    {
        session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit;
        }
    
        $user_id = $_SESSION['user']['id'];
    
        // Получаем информацию о выбранном пользователе
        $selected_user = $this->userModel->findById($selected_user_id);
        if (!$selected_user) {
            echo "Пользователь не найден";
            exit;
        }
    
        // Получаем сообщения между текущим пользователем и выбранным пользователем
        $messages = $this->messageModel->getMessages($user_id, $selected_user_id);
    
        // Передаем данные в представление
        include __DIR__ . '/../Views/messenger/index.php';
    }
    
}
