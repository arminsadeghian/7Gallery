<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Payment\PaymentService;
use App\Services\Payment\Requests\IDPayRequest;

class PaymentController extends Controller
{
    public function pay()
    {
        $user = User::first();
        $idPayRequest = new IDPayRequest([
            'user' => $user,
            'amount' => 1000
        ]);
        $paymentService = new PaymentService(PaymentService::IDPAY, $idPayRequest);
        dd($paymentService->pay());
    }
}
