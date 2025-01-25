<?php

namespace App\Controllers;

use App\Services\OrderService;

class OrderController
{
    private OrderService $orderService;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/app.php';

        switch ($config['db']['driver']) {
            case 'mysql':
            case 'pgsql':
                $dsn = sprintf(
                    '%s:host=%s;port=%s;dbname=%s',
                    $config['db']['driver'],
                    $config['db']['host'],
                    $config['db']['port'],
                    $config['db']['database']
                );
                $db = new \PDO($dsn, $config['db']['username'], $config['db']['password']);
                $orderRepository = new \App\Repositories\DatabaseOrderRepository($db);
                break;
            default:
                $orderRepository = new \App\Repositories\FileOrderRepository();
                break;
        }

        $this->orderService = new OrderService($orderRepository);
    }

    public function createOrder()
    {
        // Получаем данные запроса
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $items = $data['items'] ?? [];
            $order = $this->orderService->createOrder($items);

            // Возвращаем ответ
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($order->toArray());
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function addItems($vars)
    {
        $order_id = $vars['order_id'];
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $items = $data ?? [];
            $this->orderService->addItems($order_id, $items);

            // Возвращаем успех без содержимого
            http_response_code(204);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getOrder($vars)
    {
        $order_id = $vars['order_id'];

        try {
            $order = $this->orderService->getOrder($order_id);

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($order->toArray());
        } catch (\Exception $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function setOrderDone($vars)
    {
        $config = require __DIR__ . '/../../config/app.php';

        // Проверка авторизации
        $authMiddleware = new \App\Middleware\AuthMiddleware($config['auth_key']);
        if (!$authMiddleware->handle()) {
            return;
        }

        $order_id = $vars['order_id'];

        try {
            $this->orderService->setOrderDone($order_id);

            http_response_code(204);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getOrders()
    {
        $config = require __DIR__ . '/../../config/app.php';

        // Проверка авторизации
        $authMiddleware = new \App\Middleware\AuthMiddleware($config['auth_key']);
        if (!$authMiddleware->handle()) {
            return;
        }

        $done = $_GET['done'] ?? null;
        $done = $done !== null ? filter_var($done, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;

        try {
            $orders = $this->orderService->getOrders($done);

            $response = array_map(function ($order) {
                return [
                    'order_id' => $order->order_id,
                    'done' => $order->done,
                ];
            }, $orders);

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}