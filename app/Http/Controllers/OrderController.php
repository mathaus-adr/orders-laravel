<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderCollection;
use Illuminate\Http\Request;
use Orders\Domain\Services\ClientOrderListService;

class OrderController extends Controller
{
    public function __construct(private readonly ClientOrderListService $clientOrderListService)
    {

    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $client_id)
    {
        return new OrderCollection($this->clientOrderListService->execute(
            externalClientId: $client_id
        ));
    }
}
