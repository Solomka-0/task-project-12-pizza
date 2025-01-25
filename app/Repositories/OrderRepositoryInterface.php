<?php

namespace App\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function create(Order $order): bool;
    public function findById(string $order_id): ?Order;
    public function update(Order $order): bool;
    public function findAll(?bool $done = null): array;
}