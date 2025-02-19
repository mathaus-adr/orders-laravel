<?php

namespace App\Infrastructure\Repositories;

use App\Models\Client as ClientLaravelModel;
use Orders\Domain\Entities\Client;
use Orders\Domain\Interfaces\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{

    public function find(string $clientExternalId): ?Client
    {
        $client = ClientLaravelModel::query()->where('external_client_id', $clientExternalId)->first();

        if ($client) {
            return new Client($client->toArray());
        }

        return null;
    }

    public function create(string $clientExternalId): Client
    {
        $client = ClientLaravelModel::create([
            'external_client_id' => $clientExternalId
        ]);

        return new Client($client->toArray());
    }
}
