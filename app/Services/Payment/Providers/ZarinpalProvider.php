<?php

namespace App\Services\Payment\Providers;

use App\Services\Payment\Contracts\BaseProvider;
use App\Services\Payment\Contracts\PaymentInterface;
use App\Services\Payment\Contracts\VerifyInterface;

class ZarinpalProvider extends BaseProvider implements PaymentInterface, VerifyInterface
{

    public function payment()
    {
        // TODO: Implement pay() method.
    }

    public function verify()
    {
        // TODO: Implement verify() method.
    }
}
