<?php

namespace App\Services\Payment\Requests;

use App\Services\Payment\Contracts\RequestInterface;

class IDPayRequest implements RequestInterface
{
    private $user;
    private $amount;
    private $orderId;
    private $apiKey;

    public function __construct(array $data)
    {
        $this->user = $data['user'];
        $this->amount = $data['amount'];
        $this->orderId = $data['orderId'];
        $this->apiKey = $data['api_key'];
    }

    public function request()
    {
        // TODO: Implement request() method.
    }

    public function getUser(): mixed
    {
        return $this->user;
    }

    public function getAmount(): float|int
    {
//        return $this->amount;
        return $this->amount * 10;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getApiKey(): mixed
    {
        return $this->apiKey;
    }


}
