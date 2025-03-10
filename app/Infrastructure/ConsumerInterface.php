<?php

namespace App\Infrastructure;

interface ConsumerInterface
{
    public function createConsumer(string $host, string $port, string $user, string $password);
}
