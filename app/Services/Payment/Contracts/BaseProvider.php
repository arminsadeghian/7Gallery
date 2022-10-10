<?php

namespace App\Services\Payment\Contracts;

abstract class BaseProvider
{
    public function __construct(RequestInterface $request)
    {
    }
}
