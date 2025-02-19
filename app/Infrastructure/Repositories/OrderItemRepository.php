<?php

namespace App\Infrastructure\Repositories;

use App\Models\OrderItem as OrderItemLaravelModel;
use Orders\Domain\DataTransferObjects\OrderItemDataDTO;
use Orders\Domain\Entities\Order;
use Orders\Domain\Entities\OrderItem;
use Orders\Domain\Interfaces\Repositories\OrderItemRepositoryInterface;

class OrderItemRepository implements OrderItemRepositoryInterface
{

    public function create(Order $order, OrderItemDataDTO $orderItemDataDTO): OrderItem
    {
        $orderItem = OrderItemLaravelModel::create([
            'order_id' => $order->id,
            'name' => $orderItemDataDTO->sku,
            'quantity' => $orderItemDataDTO->quantity,
            'price' => $orderItemDataDTO->price,
        ]);

        return new OrderItem($orderItem->toArray());
    }
}
