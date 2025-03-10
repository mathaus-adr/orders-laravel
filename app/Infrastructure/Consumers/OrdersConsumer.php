<?php

namespace App\Infrastructure\Consumers;

use App\Infrastructure\ConsumerInterface;
use Orders\Domain\Interfaces\Repositories\ClientRepositoryInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class OrdersConsumer implements ConsumerInterface
{
    /**
     * @throws \Exception
     */
    public function createConsumer(string $host, string $port, string $user, string $password): AMQPStreamConnection
    {
        return new AMQPStreamConnection($host, $port, $user, $password);
    }

}
