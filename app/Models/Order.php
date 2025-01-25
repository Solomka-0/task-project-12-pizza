<?php

namespace App\Models;

class Order
{
    public string $order_id;
    public array $items;
    public bool $done;

    public function __construct(string $order_id, array $items, bool $done = false)
    {
        $this->order_id = $order_id;
        $this->items = $items;
        $this->done = $done;
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->order_id,
            'items' => $this->items,
            'done' => $this->done,
        ];
    }
}