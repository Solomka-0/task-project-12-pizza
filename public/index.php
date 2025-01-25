<?php

require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// Загрузка переменных окружения
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Загрузка конфигурации
$config = require __DIR__ . '/../config/app.php';

// Остальной код маршрутизации и обработки запросов
// Реализуем роутер
$dispatcher = simpleDispatcher(require __DIR__ . '/../routes/web.php');

// Получаем HTTP-метод и URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Разбиваем URI по '?', чтобы убрать query string
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

// Декодируем URI
$uri = rawurldecode($uri);

// Диспетчеризация
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // Создаем инстансы необходимых классов, если нужно
        // Передаем конфигурацию в контроллеры/сервисы через конструкторы или DI-контейнер

        // Вызываем обработчик
        call_user_func_array($handler, [$vars]);
        break;
}