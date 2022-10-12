<?php

namespace App\Services\Payment\Requests;

use App\Services\Payment\Contracts\RequestInterface;

class IDPayVerifyRequest implements RequestInterface
{
    private $id;
    private $orderId;
    private $apiKey;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->orderId = $data['orderId'];
        $this->apiKey = $data['api_key'];
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getOrderId(): mixed
    {
        return $this->orderId;
    }

    public function getApiKey(): mixed
    {
        return $this->apiKey;
    }

    public function request()
    {
        // TODO: Implement request() method.
    }
}
