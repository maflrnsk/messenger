<?php
// profile.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Профиль</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  <!-- Подключаем jQuery -->
</head>
<body>
    <header>
        <img src="/images/message.png">
        <div>
            <a href="/messenger">Мессенджер</a>
            <a href="/profile">Профиль</a>
        </div>
    </header>
    <div class="profile-body">
        <?php if (!empty($user['profile_picture'])): ?>
            <img id="profile-picture" src="/uploads/<?= htmlspecialchars(basename($user['profile_picture'])) ?>" alt="Фото профиля">
        <?php endif; ?>

        <p>Имя пользователя: <span id="username"><?= htmlspecialchars($user['username']) ?></span></p>
        <p>Почта: <?= htmlspecialchars($user['email']) ?></p>

        <form id="profile-form" action="/profile" method="post" enctype="multipart/form-data">
            <input type="text" name="username" id="username-input" placeholder="Введите новое имя" value="<?= htmlspecialchars($user['username']) ?>">
            <input type="password" name="password" id="password-input" placeholder="Введите новый пароль">
            <input type="file" name="profile_picture" id="profile-picture-input">
            <div class="buttons">
                <button type="submit">Обновить</button>
                <button type="button" onclick="window.location.href='/logout'">Выйти</button>
            </div> 
        </form>

        <!-- Сообщения выводятся через alert, а не в блоке #message -->
    </div>

    <script>
       $(document).ready(function() {
    $('#profile-form').on('submit', function(event) {
        event.preventDefault(); // предотвращаем стандартное поведение формы (перезагрузка страницы)

        var formData = new FormData(this); // Собираем все данные формы

        $.ajax({
            url: '/profile', // URL для отправки данных
            type: 'POST',
            data: formData,
            processData: false, // не нужно обрабатывать данные
            contentType: false, // не нужно устанавливать content-type
            success: function(response) {
                // Обновление информации на странице
                if (response.success) {
                    $('#username').text(response.username); // Обновляем имя пользователя
                    if (response.profile_picture) {
                        // Обновляем фото профиля, если новое фото было загружено
                        $('#profile-picture').attr('src', '/uploads/' + response.profile_picture);
                    }
                    alert(response.message); // Сообщение об успехе через alert
                } else {
                    alert(response.error); // Сообщение об ошибке через alert
                }
            },
            error: function() {
                alert('Произошла ошибка. Попробуйте позже.'); // Сообщение об ошибке через alert
            }
        });
    });
});

    </script>
</body>
</html>
