<?php

namespace App\Services\Payment\Providers;

use App\Services\Payment\Contracts\BaseProvider;
use App\Services\Payment\Contracts\Payable;
use App\Services\Payment\Contracts\Verifiable;

class IDPayProvider extends BaseProvider implements Payable, Verifiable
{

    public function pay()
    {
        dd('Hi From IDPay');
    }

    public function verify()
    {
        // TODO: Implement verify() method.
    }
}
