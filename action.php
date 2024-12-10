<?php
// Подключение модели сообщений
use App\Models\Message;

// Проверка, что пользователь авторизован
session_start();
if (!isset($_SESSION['user'])) {
    echo "Вы не авторизованы!";
    exit;
}

// Получение текущего пользователя
$user_id = $_SESSION['user']['id'];

// Обработчик действия (удаление, редактирование, пересылка)
$action = $_GET['action'] ?? null;
$message_id = $_GET['message_id'] ?? null;
$selected_user_id = $_GET['user'] ?? null;

if ($action && $message_id) {
    // Подключаем модель сообщений
    $messageModel = new Message($db);

    switch ($action) {
        case 'delete':
            // Удаление сообщения
            $messageModel->deleteMessage($message_id, $user_id);
            echo "Сообщение удалено.";
            break;

        case 'edit':
            // Редактирование сообщения
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $new_content = $_POST['new_content'] ?? '';
                if ($new_content) {
                    $messageModel->editMessage($message_id, $new_content, $user_id);
                    echo "Сообщение отредактировано.";
                } else {
                    echo "Необходимо ввести новый текст сообщения.";
                }
            }
            break;

        case 'forward':
            // Пересылка сообщения
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $new_receiver_id = $_POST['new_receiver_id'] ?? null;
                if ($new_receiver_id) {
                    $messageModel->forwardMessage($message_id, $new_receiver_id, $user_id);
                    echo "Сообщение переслано.";
                } else {
                    echo "Необходимо выбрать нового получателя.";
                }
            }
            break;

        default:
            echo "Неверное действие.";
    }
} else {
    echo "Не указано сообщение или действие.";
}
?>
