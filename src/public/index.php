<?php
require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/Router/Router.php';
require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/RegisterController.php';
require_once __DIR__ . '/../app/Controllers/LoginController.php';
require_once __DIR__ . '/../app/Controllers/MessengerController.php';  // Контроллер для мессенджера
require_once __DIR__ . '/../app/Controllers/ProfileController.php';
require_once __DIR__ . '/../app/Controllers/LogoutController.php';


use App\Router\Router;
use App\Controllers\HomeController;

$router = new Router();

// Главная страница
$router->add('GET', '/', [HomeController::class, 'index']);

// Страницы входа и регистрации
$router->add('GET', '/login', [\App\Controllers\LoginController::class, 'showLoginForm']);
$router->add('POST', '/login', [\App\Controllers\LoginController::class, 'login']);

$router->add('GET', '/register', [\App\Controllers\RegisterController::class, 'showRegisterForm']);
$router->add('POST', '/register', [\App\Controllers\RegisterController::class, 'register']);

// В файле src/public/index.php
$router->add('GET', '/profile', [\App\Controllers\ProfileController::class, 'showProfile']);
$router->add('POST', '/profile', [\App\Controllers\ProfileController::class, 'updateProfile']);


$router->add('GET', '/messenger', [App\Controllers\MessengerController::class, 'index']);
$router->add('POST', '/messenger', [App\Controllers\MessengerController::class, 'sendMessage']);

$router->add('GET', '/messenger/user/(\d+)', [App\Controllers\MessengerController::class, 'showMessages']);

// Добавьте маршрут для выхода
// Добавьте это в ваш файл index.php в разделе маршрутов
$router->add('GET', '/logout', [\App\Controllers\LogoutController::class, 'logout']);

$router->run();