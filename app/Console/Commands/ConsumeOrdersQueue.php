<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Orders\Domain\DataTransferObjects\OrderDataDTO;
use Orders\Domain\Services\CreateOrderService;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumeOrdersQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:consume-orders-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $amqpConnection = app(AMQPStreamConnection::class);
        $channel = $amqpConnection->channel();

        $channel->queue_declare('laravel', false, true, false, false);
        $channel->exchange_declare('laravel', 'topic', false, true, false);
        $channel->queue_bind('laravel', 'laravel');


        $createOrderService = app(CreateOrderService::class);

        $channel->basic_consume(
            queue: 'laravel',
            consumer_tag: 'laravel',
            no_ack: true,
            callback: function (AMQPMessage $message) use ($createOrderService) {
                $body = json_decode($message->getBody(), true);
                $orderDataDto = new OrderDataDTO($body);
                $createOrderService->execute($orderDataDto);
            }
        );

        $channel->consume(15);

        $channel->close();
        $amqpConnection->close();

    }
}
