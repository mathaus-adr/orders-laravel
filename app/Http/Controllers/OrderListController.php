<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderCollection;
use Illuminate\Http\Request;
use Orders\Domain\Services\ListOrdersService;

class OrderListController extends Controller
{
    public function __construct(private ListOrdersService $listOrdersService)
    {

    }

    public function __invoke(Request $request)
    {
//        dd( $request->query('client_external_id'),
//            $request->query('order_external_id'));
        return new OrderCollection($this->listOrdersService->execute(
            externalClientId: $request->query('client_external_id'),
            externalOrderId: $request->query('order_external_id')
        ));
    }
}
