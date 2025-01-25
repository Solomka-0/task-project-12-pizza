<?php

namespace App\Validators;

class OrderValidator
{
    public static function validateItems(array $items): bool
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Items cannot be empty');
        }

        foreach ($items as $item) {
            if (!is_int($item) || $item < 1 || $item > 5000) {
                throw new \InvalidArgumentException('Invalid item value: ' . $item);
            }
        }

        return true;
    }

    public static function validateOrderId(string $order_id): bool
    {
        $length = strlen($order_id);
        if ($length < 3 || $length > 15) {
            throw new \InvalidArgumentException('order_id must be between 3 and 15 characters');
        }

        if (!preg_match('/^\w+$/', $order_id)) {
            throw new \InvalidArgumentException('order_id contains invalid characters');
        }

        return true;
    }
}