<?php

namespace App\Infrastructure\Repositories;

use App\Models\Order as OrderLaravelModel;
use Orders\Domain\DataTransferObjects\OrderDataDTO;
use Orders\Domain\Entities\Client;
use Orders\Domain\Entities\Collections\EntityPaginatedCollection;
use Orders\Domain\Entities\Order;
use Orders\Domain\Interfaces\Repositories\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{

    public function create(Client $client, OrderDataDTO $orderDataDTO): Order
    {
        $order = OrderLaravelModel::create([
            'client_id' => $client->id,
            'total' => $orderDataDTO->getOrderTotal(),
            'external_order_id' => $orderDataDTO->orderExternalId,
        ]);

        return new Order($order->toArray());
    }

    public function getOrderListPaginatedByExternalClientId(
        string $externalClientId
    )
    {
        return OrderLaravelModel::query()->join('clients', 'orders.client_id', '=', 'clients.id')
            ->where('clients.external_client_id', $externalClientId)
            ->select(['orders.*'])->with(['orderItems'])->paginate();
    }

    public function getOrderListByParam(string $externalOrderId = null, string $externalClientId = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return OrderLaravelModel::query()
            ->when($externalOrderId, function ($query) use ($externalOrderId) {
                return $query->where('external_order_id', $externalOrderId);
            })
            ->when($externalClientId, function ($query) use ($externalClientId) {
                return $query->join('clients', 'orders.client_id', '=', 'clients.id')
                    ->where('clients.external_client_id', $externalClientId);
            })
            ->select(['orders.*'])->with(['orderItems'])->paginate();
    }
}
