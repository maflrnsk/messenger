<?php
$servername = "db";  // Хост, возможно, это контейнер базы данных в Docker
$username = "user";   // Имя пользователя для подключения
$password = "password"; // Пароль
$dbname = "messenger_db"; // Имя базы данных

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка на ошибки подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Возвращаем объект соединения
return $conn;

