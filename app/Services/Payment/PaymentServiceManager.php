<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\RequestInterface;
use App\Services\Payment\Exceptions\ProviderClassNotFoundException;

class PaymentServiceManager
{
    private const PROVIDERS_BASE_NAMESPACE = "App\\Services\\Payment\\Providers\\";
    public const IDPAY = 'IDPayProvider';
    public const ZARINPAL = 'ZarinpalProvider';

    public function __construct(private string $providerName, private RequestInterface $request)
    {
    }

    public function pay()
    {
        try {
            return $this->findProvider()->pay();
        } catch (ProviderClassNotFoundException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @throws ProviderClassNotFoundException
     */
    private function findProvider()
    {
        $className = self::PROVIDERS_BASE_NAMESPACE . $this->providerName;

        if (!class_exists($className)) {
            throw new ProviderClassNotFoundException('درگاه پرداخت انتخاب شده پیدا نشد');
        }

        return new $className($this->request);
    }

}
