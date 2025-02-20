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
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
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
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
