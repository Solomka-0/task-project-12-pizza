<?php

namespace App\Repositories;

use App\Models\Order;
use PDO;

class FileOrderRepository implements OrderRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/../../storage/data.json';
    }

    private function readData(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $data = json_decode(file_get_contents($this->filePath), true);
        return $data ?: [];
    }

    private function writeData(array $data): void
    {
        file_put_contents($this->filePath, json_encode($data));
    }

    public function create(Order $order): bool
    {
        $data = $this->readData();
        $data[$order->order_id] = $order->toArray();
        $this->writeData($data);
        return true;
    }

    public function findById(string $order_id): ?Order
    {
        $data = $this->readData();
        if (!isset($data[$order_id])) {
            return null;
        }
        $orderData = $data[$order_id];
        return new Order($orderData['order_id'], $orderData['items'], $orderData['done']);
    }

    public function update(Order $order): bool
    {
        $data = $this->readData();
        if (!isset($data[$order->order_id])) {
            return false;
        }
        $data[$order->order_id] = $order->toArray();
        $this->writeData($data);
        return true;
    }

    public function findAll(?bool $done = null): array
    {
        $data = $this->readData();
        $orders = [];

        foreach ($data as $orderData) {
            if ($done === null || $orderData['done'] === $done) {
                $orders[] = new Order($orderData['order_id'], $orderData['items'], $orderData['done']);
            }
        }

        return $orders;
    }
}