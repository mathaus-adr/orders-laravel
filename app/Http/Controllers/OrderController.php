<?php

/**
 * This controller handles operations related to orders.
 */

namespace App\Http\Controllers;

use /**
 * Class OrderCollection
 *
 * Represents a custom resource collection for orders in the application.
 * Responsible for transforming order models into a JSON response.
 *
 * This class utilizes Laravel's resource collection handling capabilities to convert
 * a collection of order models into a structured format for API responses.
 *
 * @package App\Http\Resources
 */
    App\Http\Resources\OrderCollection;
use /**
 * Class Request
 *
 * This class extends Symfony's HTTP request and adds functionalities tailored to Laravel's needs,
 * such as retrieving input data, headers, cookies, JSON payload, and file uploads. It also provides
 * methods for request validation and examining the request's HTTP status, format, and more.
 *
 * @package Illuminate\Http
 * @mixin \Symfony\Component\HttpFoundation\Request
 *
 * @method static array capture() Capture and create a new request instance.
 * @method bool isJson() Determine if the current request is sending JSON.
 * @method
 */
    Illuminate\Http\Request;
use /**
 * ClientOrderListService is responsible for handling operations related to listing client orders
 * in the Orders domain of the application.
 */
    Orders\Domain\Services\ClientOrderListService;

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
