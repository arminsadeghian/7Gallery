<?php

namespace App\Services\Payment\Providers;

use App\Services\Payment\Contracts\BaseProvider;
use App\Services\Payment\Contracts\Payable;
use App\Services\Payment\Contracts\Verifiable;

class IDPayProvider extends BaseProvider implements Payable, Verifiable
{

    public function pay()
    {
        // TODO: Implement pay() method.
    }

    public function verify()
    {
        // TODO: Implement verify() method.
    }
}
