<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepositoryInterface;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function createOrder(array $items): Order
    {
        // Валидация
        if (empty($items)) {
            throw new \InvalidArgumentException('Items cannot be empty');
        }

        // Генерация уникального order_id
        $order_id = $this->generateOrderId();
        $order = new Order($order_id, $items);

        $this->orderRepository->create($order);

        return $order;
    }

    public function addItems(string $order_id, array $items): void
    {
        $order = $this->orderRepository->findById($order_id);

        if (!$order) {
            throw new \Exception('Order not found');
        }

        if ($order->done) {
            throw new \Exception('Cannot modify a completed order');
        }

        $order->items = array_merge($order->items, $items);
        $this->orderRepository->update($order);
    }

    public function getOrder(string $order_id): Order
    {
        $order = $this->orderRepository->findById($order_id);

        if (!$order) {
            throw new \Exception('Order not found');
        }

        return $order;
    }

    public function setOrderDone(string $order_id): void
    {
        $order = $this->orderRepository->findById($order_id);

        if (!$order) {
            throw new \Exception('Order not found');
        }

        if ($order->done) {
            throw new \Exception('Order is already completed');
        }

        $order->done = true;
        $this->orderRepository->update($order);
    }

    public function getOrders(?bool $done = null): array
    {
        return $this->orderRepository->findAll($done);
    }

    private function generateOrderId(): string
    {
        $length = rand(3, 15);
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $order_id = '';

        for ($i = 0; $i < $length; $i++) {
            $order_id .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $order_id;
    }
}