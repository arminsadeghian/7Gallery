<?php

namespace App\Services\Payment\Requests;

use App\Services\Payment\Contracts\RequestInterface;

class IDPayRequest implements RequestInterface
{
    private $user;
    private $amount;
    private $orderId;

    public function __construct(array $data)
    {
        $this->user = $data['user'];
        $this->amount = $data['amount'];
        $this->orderId = $data['orderId'];
    }

    public function request()
    {
        // TODO: Implement request() method.
    }

    public function getUser(): mixed
    {
        return $this->user;
    }

    public function getAmount(): mixed
    {
        return $this->amount;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }


}
