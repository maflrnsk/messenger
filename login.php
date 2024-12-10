<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Вход</title>
</head>
<body>
    <div class="login-body">
        <h1>Вход</h1>

        <form action="/login" method="post">
            <input type="email" name="email" required placeholder="Введите почту">
            <input type="password" name="password" required placeholder="Введите пароль">
            <div class="buttons">
                <button type="submit">Войти</button>
                <button type="button" onclick="window.location.href='/'">На главную</button>
            </div>
        </form>
    </div>

    <?php if (!empty($error_message)): ?>
        <script>
            alert("<?php echo $error_message; ?>");
        </script>
    <?php endif; ?>
</body>
</html>
