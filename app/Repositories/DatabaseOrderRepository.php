<?php

namespace App\Repositories;

use App\Models\Order;
use PDO;

class DatabaseOrderRepository implements OrderRepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(Order $order): bool
    {
        $stmt = $this->db->prepare('INSERT INTO orders (order_id, done) VALUES (:order_id, :done)');
        $stmt->execute([
            ':order_id' => $order->order_id,
            ':done' => empty($order->done) ? 0 : 1,
        ]);

        $stmt_items = $this->db->prepare('INSERT INTO order_items (order_id, item_id) VALUES (:order_id, :item_id)');
        foreach ($order->items as $item_id) {
            $stmt_items->execute([
                ':order_id' => $order->order_id,
                ':item_id' => $item_id,
            ]);
        }

        return true;
    }

    public function findById(string $order_id): ?Order
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE order_id = :order_id');
        $stmt->execute([':order_id' => $order_id]);
        $order_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order_data) {
            return null;
        }

        $stmt_items = $this->db->prepare('SELECT item_id FROM order_items WHERE order_id = :order_id');
        $stmt_items->execute([':order_id' => $order_id]);
        $items = $stmt_items->fetchAll(PDO::FETCH_COLUMN);

        $order = new Order($order_data['order_id'], $items, (bool)$order_data['done']);

        return $order;
    }

    public function update(Order $order): bool
    {
        $stmt = $this->db->prepare('UPDATE orders SET done = :done WHERE order_id = :order_id');
        $stmt->execute([
            ':order_id' => $order->order_id,
            ':done' => empty($order->done) ? 0 : 1,
        ]);

        // Удаляем старые позиции
        $stmt_delete = $this->db->prepare('DELETE FROM order_items WHERE order_id = :order_id');
        $stmt_delete->execute([':order_id' => $order->order_id]);

        // Добавляем обновленные позиции
        $stmt_items = $this->db->prepare('INSERT INTO order_items (order_id, item_id) VALUES (:order_id, :item_id)');
        foreach ($order->items as $item_id) {
            $stmt_items->execute([
                ':order_id' => $order->order_id,
                ':item_id' => $item_id,
            ]);
        }

        return true;
    }

    public function findAll(?bool $done = null): array
    {
        if ($done !== null) {
            $stmt = $this->db->prepare('SELECT * FROM orders WHERE done = :done');
            $stmt->execute([':done' => $done]);
        } else {
            $stmt = $this->db->query('SELECT * FROM orders');
        }

        $orders_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $orders = [];

        foreach ($orders_data as $order_data) {
            $stmt_items = $this->db->prepare('SELECT item_id FROM order_items WHERE order_id = :order_id');
            $stmt_items->execute([':order_id' => $order_data['order_id']]);
            $items = $stmt_items->fetchAll(PDO::FETCH_COLUMN);

            $orders[] = new Order($order_data['order_id'], $items, (bool)$order_data['done']);
        }

        return $orders;
    }
}