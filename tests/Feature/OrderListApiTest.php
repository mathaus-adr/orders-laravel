<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderListApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_if_can_list_client_orders(): void
    {
        $client = Client::factory()->create();

        $orders = Order::factory()->count(3)->create(['client_id' => $client->id]);

        foreach ($orders as $order) {
            OrderItem::factory()->forOrder($order->id)->count(3)->create();
        }

        $response = $this->getJson("/api/clients/{$client->external_client_id}/orders");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'codigoPedido',
                    'codigoCliente',
                    'itens' => [
                        '*' => [
                            'produto',
                            'quantidade',
                            'preco',
                        ],
                    ],
                    'total',
                    'created_at',
                    'updated_at',
                ],
            ],
            'quantidade',
            'total',
        ]);
    }

    /**
     * A basic feature test example.
     */
    public function test_if_can_list_orders_without_params(): void
    {
        $client = Client::factory()->create();

        $orders = Order::factory()->count(3)->create(['client_id' => $client->id]);

        foreach ($orders as $order) {
            OrderItem::factory()->forOrder($order->id)->count(3)->create();
        }

        $response = $this->get("/api/orders?" . http_build_query(['client_external_id' => $client->external_client_id]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'codigoPedido',
                    'codigoCliente',
                    'itens' => [
                        '*' => [
                            'produto',
                            'quantidade',
                            'preco',
                        ],
                    ],
                    'total',
                    'created_at',
                    'updated_at',
                ],
            ],
            'quantidade',
            'total',
        ]);

        $response->assertJsonFragment([
            'quantidade' => 3,
            'total' => $orders->sum('total')
        ]);


        $response = $this->get("/api/orders?" . http_build_query(['client_external_id' => $this->faker->numberBetween(200000, 300000)]));

        $response->assertJsonFragment([
            'quantidade' => 0,
            'total' => 0
        ]);

        foreach ($orders as $order) {
            $response = $this->get("/api/orders?" . http_build_query(['order_external_id' => $order->external_order_id]));
            $response->assertJsonFragment([
                'quantidade' => 1,
                'total' => $order->total
            ]);
        }
    }


}
