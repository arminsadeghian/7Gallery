<?php

namespace App\Services\Payment\Providers;

use App\Services\Payment\Contracts\BaseProvider;
use App\Services\Payment\Contracts\PaymentInterface;
use App\Services\Payment\Contracts\VerifyInterface;

class IDPayProvider extends BaseProvider implements PaymentInterface, VerifyInterface
{
    public function payment()
    {
        $params = [
            'order_id' => $this->request->getOrderId(),
            'amount' => $this->request->getAmount(),
            'name' => $this->request->getUser()->first_name,
            'phone' => $this->request->getUser()->phone,
            'mail' => $this->request->getUser()->email,
            'callback' => route('payment.callback'),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-API-KEY: ' . $this->request->getApiKey() . ' ',
            'X-SANDBOX: 1'
        ));

        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);

        if (isset($result['error_code'])) {
            throw new \InvalidArgumentException($result['error_message']);
        }

        return redirect()->away($result['link']);
    }

    public function verify()
    {
        // TODO: Implement verify() method.
    }
}
