<?php

namespace App\Models;

use mysqli;

class Message
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getMessages($user_id, $selected_user_id)
    {
        $sql = "SELECT id, sender_id, receiver_id, content, photo_path, timestamp 
                FROM messages 
                WHERE (sender_id = ? AND receiver_id = ?) 
                   OR (sender_id = ? AND receiver_id = ?)
                ORDER BY timestamp ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiii", $user_id, $selected_user_id, $selected_user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['photo_path']) {
                $row['photo_path'] = 'uploads/photos/' . basename($row['photo_path']);
            }
            $messages[] = $row;
        }

        return $messages;
    }

    public function sendMessage($sender_id, $receiver_id, $content, $photo_path)
    {
        $sql = "INSERT INTO messages (sender_id, receiver_id, content, photo_path) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiss", $sender_id, $receiver_id, $content, $photo_path);
        $stmt->execute();
    }

    public function deleteMessage($message_id, $user_id)
    {
        $sql = "DELETE FROM messages WHERE id = ? AND sender_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $message_id, $user_id);
        $stmt->execute();
    }

    public function editMessage($message_id, $new_content, $user_id)
    {
        $sql = "UPDATE messages SET content = ? WHERE id = ? AND sender_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $new_content, $message_id, $user_id);
        $stmt->execute();
    }

    public function forwardMessage($message_id, $new_receiver_id, $user_id)
    {
        $sql = "SELECT content, photo_path FROM messages WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $original_message = $result->fetch_assoc();

        if ($original_message) {
            $sql = "INSERT INTO messages (sender_id, receiver_id, content, photo_path) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iiss", $user_id, $new_receiver_id, $original_message['content'], $original_message['photo_path']);
            $stmt->execute();
        }
    }
}

