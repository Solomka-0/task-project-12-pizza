<?php

use App\Controllers\OrderController;

return function (FastRoute\RouteCollector $r) {
    $orderController = new OrderController();

    // Создание заказа
    $r->addRoute('POST', '/orders', [$orderController, 'createOrder']);

    // Добавление товаров в заказ
    $r->addRoute('POST', '/orders/{order_id:\w{3,15}}/items', [$orderController, 'addItems']);

    // Получение информации о заказе
    $r->addRoute('GET', '/orders/{order_id:\w{3,15}}', [$orderController, 'getOrder']);

    // Пометка заказа как выполненного (с проверкой авторизации)
    $r->addRoute('POST', '/orders/{order_id:\w{3,15}}/done', [$orderController, 'setOrderDone']);

    // Получение списка заказов (с проверкой авторизации)
    $r->addRoute('GET', '/orders', [$orderController, 'getOrders']);
};