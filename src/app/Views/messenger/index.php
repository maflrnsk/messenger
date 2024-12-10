<?php
// app/Views/messenger/index.php
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Мессенджер</title>
</head>
<body>
    <!-- Заголовок -->
    <header>
        <img src="../images/message.png" alt="Мессенджер">
        <div>
            <a href="/messenger">Мессенджер</a>
            <a href="/profile">Профиль</a>
        </div>
    </header>

    <!-- Основная часть с чатами -->
    <div class="messenger-body">
        <!-- Список пользователей -->
        <div class="chat-list">
            <h2 class="chat-list-header">Пользователи</h2>
            <ul>
                <?php if (isset($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <li>
                            <a href="/messenger/user/<?= htmlspecialchars($user['id']) ?>">
                                <?= htmlspecialchars($user['username'] ?? $user['email']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Окно чата -->
        <div class="chat-window">
            <?php if (isset($selected_user)): ?>
                <div class="messages" id="messages">
                    <!-- Сообщения будут загружаться сюда с помощью JS -->
                    <?php foreach ($messages as $msg): ?>
                        <div class="message <?= $msg['sender_id'] == $_SESSION['user']['id'] ? 'from-me' : 'from-them' ?>">
                            <p><?= htmlspecialchars($msg['content']) ?></p>
                            <?php if ($msg['photo_path']): ?>
                                <img src="<?= htmlspecialchars($msg['photo_path']) ?>" alt="Фото" />
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Форма для отправки сообщений -->
                <form action="/messenger" method="POST" enctype="multipart/form-data">
                    <input type="text" name="message" placeholder="Введите сообщение" required>
                    <input type="file" name="photo" accept="image/*" id="photo-input">
                    <button type="submit">Отправить</button>
                </form>
            <?php else: ?>
                <p>Выберите пользователя для начала переписки.</p>
            <?php endif; ?>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chatWindow = document.querySelector('.messages');
            const messageForm = document.querySelector('.message-input');
            const inputField = messageForm.querySelector('input[name="message"]');
            const selectedUserId = new URLSearchParams(window.location.search).get('user');

            let isScrolledToBottom = true;

            // Функция проверки прокрутки
            function checkScrollPosition() {
                const chatHeight = chatWindow.scrollHeight;
                const scrollPosition = chatWindow.scrollTop + chatWindow.clientHeight;
                isScrolledToBottom = (scrollPosition >= chatHeight - 10);
            }

            // Загрузка сообщений
            async function fetchMessages() {
                try {
                    const response = await fetch(`message_actions.php?action=get_messages&user=${selectedUserId}`);
                    const messages = await response.json();

                    chatWindow.innerHTML = '';  // Очищаем старые сообщения

                    // Создаем элементы сообщений
                    messages.forEach(msg => {
                        const msgElement = document.createElement('div');
                        const sender = msg.sender_id == <?= $user_id ?> ? 'from-me' : 'from-them';
                        msgElement.classList.add('message', sender);
                        msgElement.setAttribute('data-id', msg.id);

                        if (msg.photo_path) {
                            const imgElement = document.createElement('img');
                            imgElement.src = msg.photo_path;
                            imgElement.style.width = '100%';  // Изображение растягивается на 100% по ширине
                            imgElement.style.height = '100%'; // Изображение растягивается на 100% по высоте
                            imgElement.style.objectFit = 'cover'; // Для сохранения пропорций изображения

                            msgElement.style.width = '200px';  // Фиксированная ширина
                            msgElement.style.height = '200px'; // Фиксированная высота
                            msgElement.appendChild(imgElement);
                        } else if (msg.content) {
                            const textContent = document.createElement('p');
                            textContent.textContent = msg.content;

                            msgElement.appendChild(textContent);
                        }

                        chatWindow.appendChild(msgElement);
                    });

                    // Прокручиваем чат вниз, если пользователь внизу
                    if (isScrolledToBottom) {
                        chatWindow.scrollTop = chatWindow.scrollHeight;
                    }
                } catch (error) {
                    console.error('Ошибка при загрузке сообщений:', error);
                }
            }

            // Обработчик отправки сообщений
            messageForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const formData = new FormData(messageForm);

                try {
                    const response = await fetch('message_actions.php', {
                        method: 'POST',
                        body: formData // FormData автоматически включает файл и текстовые данные
                    });

                    if (!response.ok) {
                        throw new Error('Ошибка при отправке сообщения');
                    }

                    inputField.value = ''; // Очистка поля ввода
                    document.querySelector('#photo-input').value = ''; // Очистка поля загрузки фото
                    fetchMessages(); // Перезагружаем сообщения
                } catch (error) {
                    console.error('Ошибка отправки сообщения:', error);
                }
            });

            // Отслеживание прокрутки
            chatWindow.addEventListener('scroll', checkScrollPosition);

            // Загрузка сообщений при инициализации
            fetchMessages();
        });
    </script>
</body>
</html>

