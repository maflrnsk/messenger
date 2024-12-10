<?php if (!empty($error_message)): ?>
    <script>alert("<?php echo $error_message; ?>");</script>
<?php endif; ?>

<?php if (!empty($success_message)): ?>
    <script>alert("<?php echo $success_message; ?>");</script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Регистрация</title>
</head>
<body>
    <div class="register-body">
        <h1>Регистрация</h1>

        <form action="/register" method="post">
            <input type="text" name="username" required placeholder="Введите имя">
            <input type="email" name="email" required placeholder="Введите почту">
            <input type="password" name="password" required placeholder="Введите пароль">
            <div class="buttons">
                <button type="submit">Зарегистрироваться</button>
                <button type="button" onclick="window.location.href='/'">На главную</button>
            </div>
        </form>
    </div>
</body>
</html>
