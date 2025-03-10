<?php

namespace Tests\Feature;

use App\Infrastructure\ConsumerInterface;
use App\Infrastructure\Consumers\OrdersConsumer;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Tests\TestCase;

class ConsumeOrdersQueueCommandTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_consume_orders_queue_command(): void
    {
        $ordersConsumerMock = Mockery::mock(OrdersConsumer::class);
        $this->app->instance(ConsumerInterface::class, $ordersConsumerMock);

        $amqpConnectionMock = Mockery::mock(AMQPStreamConnection::class);

        $ordersConsumerMock->shouldReceive('createConsumer')
            ->once()
            ->with('rabbitmq', '5672', 'admin', 'admin')
            ->andReturn($amqpConnectionMock);

        $amqpChannelMock = Mockery::mock(AMQPChannel::class);
        $amqpConnectionMock->shouldReceive('channel')->andReturn($amqpChannelMock);

        $body = [
            'codigoPedido' => $this->faker->numberBetween(3000, 9000),
            'codigoCliente' => $this->faker->numberBetween(3000, 9000),
            'itens' => [
                [
                    'produto' => $this->faker->word,
                    'quantidade' => $this->faker->randomFloat(2, 10, 1000),
                    'preco' => $this->faker->randomFloat(2, 10, 1000)
                ],
                [
                    'produto' => $this->faker->word,
                    'quantidade' => $this->faker->randomFloat(2, 10, 1000),
                    'preco' => $this->faker->randomFloat(2, 10, 1000)
                ]
            ]
        ];

        $messageBody = json_encode($body);

        $totalPreco = array_reduce($body['itens'], function ($sum, $item) {
            return $sum + $item['preco'];
        }, 0);

        $amqpMessageMock = new AMQPMessage($messageBody);

        $amqpChannelMock->shouldReceive('queue_declare')
            ->once()
            ->with('laravel', false, true, false, false);

        $amqpChannelMock->shouldReceive('exchange_declare')
            ->once()
            ->with('laravel', 'topic', false, true, false);

        $amqpChannelMock->shouldReceive('queue_bind')
            ->once()
            ->with('laravel', 'laravel');

        $amqpChannelMock->shouldReceive('basic_consume')
            ->once()
            ->withArgs(function ($queue, $consumerTag, $noLocal, $noAck, $exclusive, $nowait, $callback) use ($amqpMessageMock) {
                $callback($amqpMessageMock);
                return true;
            });

        // Simulate the consume method
        $amqpChannelMock->shouldReceive('consume')
            ->once()
            ->with(15);

        $amqpChannelMock->shouldReceive('close')
            ->once();
        $amqpConnectionMock->shouldReceive('close')
            ->once();

        // Execute the command
        $this->artisan('app:consume-orders-queue')
            ->assertExitCode(0);

        $client = Client::where('external_client_id', $body['codigoCliente'])->first();

        $this->assertDatabaseHas('orders', [
            'external_order_id' => $body['codigoPedido'],
            'client_id' => $client->id,
            'total' => $totalPreco
        ]);

        $order = Order::where('external_order_id', $body['codigoPedido'])->first();

        foreach ($body['itens'] as $item) {
            $this->assertDatabaseHas('order_items', [
                'name' => $item['produto'],
                'quantity' => $item['quantidade'],
                'price' => $item['preco'],
                'order_id' => $order->id
            ]);
        }

        $this->assertDatabaseHas('clients', [
            'external_client_id' => $body['codigoCliente']
        ]);
    }
}
