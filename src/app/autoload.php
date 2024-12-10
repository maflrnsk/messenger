<?php

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';

    // Проверяем, использует ли класс пространство имен App\
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    // Убираем префикс пространства имен
    $relativeClass = substr($class, strlen($prefix));

    // Заменяем пространства имен на пути файлов
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    // Если файл существует, подключаем его
    if (file_exists($file)) {
        require $file;
    }
});
?>