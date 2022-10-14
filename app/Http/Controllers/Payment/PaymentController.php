<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\PayRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\Payment\PaymentService;
use App\Services\Payment\Requests\IDPayRequest;
use App\Services\Payment\Requests\IDPayVerifyRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PaymentController extends Controller
{
    public function pay(PayRequest $request)
    {
        $validatedData = $request->validated();

        $createdUser = User::firstOrCreate([
            'email' => $validatedData['email'],
        ], [
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'mobile' => $validatedData['mobile'],
        ]);

        try {
            $orderItems = json_decode(Cookie::get('7Gallery_cart'), true);

            if (count($orderItems) <= 0) {
                throw new \InvalidArgumentException('سبد خرید شما خالی است');
            }

            $products = Product::findMany(array_keys($orderItems));
            $productsTotalPrice = $products->sum('price');
            $referenceCode = sha1(time() . rand(1111, 9999));

            $createdOrder = Order::create([
                'user_id' => $createdUser->id,
                'amount' => $productsTotalPrice,
                'reference_code' => $referenceCode,
                'status' => 'unpaid'
            ]);

            $orderItemsForCreatedOrder = $products->map(function ($product) {
                $currentProduct = $product->only(['price', 'id']);
                $currentProduct['product_id'] = $currentProduct['id'];
                unset($currentProduct['id']);
                return $currentProduct;
            });

            $createdOrder->orderItems()->createMany($orderItemsForCreatedOrder->toArray());

            Payment::create([
                'order_id' => $createdOrder->id,
                'status' => 'unpaid',
                'gateway' => 'idpay',
                'reference_code' => $referenceCode,
            ]);

            $idPayRequest = new IDPayRequest([
                'user' => $createdUser,
                'amount' => $productsTotalPrice,
                'orderId' => $referenceCode,
                'api_key' => config('services.gateways.idpay.api_key')
            ]);

            $paymentService = new PaymentService(PaymentService::IDPAY, $idPayRequest);

            return $paymentService->pay();

        } catch (Exception $e) {
            return back()->with('failed', $e->getMessage());
        }

    }

    public function callback(Request $request)
    {
        $paymentInfo = $request->all();
        $idPayVerifyRequest = new IDPayVerifyRequest([
            'id' => $paymentInfo['id'],
            'orderId' => $paymentInfo['order_id'],
            'api_key' => config('services.gateways.idpay.api_key')
        ]);

        $paymentService = new PaymentService(PaymentService::IDPAY, $idPayVerifyRequest);
        $result = $paymentService->verify();

        if ($result['status'] == false) {
            return redirect()->route('home.checkout.show')->with('failed', 'پرداخت انجام نشد');
        }

//        if ($result['status'] == 101) {
//            return redirect()->route('home.checkout.show')->with('failed', 'پرداخت قبلا انجام شده است');
//        }

        $currentPayment = Payment::where('reference_code', $result['data']['order_id'])->first();

        $currentPayment->update([
            'status' => 'paid',
            'transaction_id' => $result['data']['track_id']
        ]);

        $currentPayment->order()->update([
            'status' => 'paid',
        ]);

        $reservedImages = $currentPayment->order->orderItems->map(function ($orderItem) {
            return $orderItem->product->source_url;
        });

        dd($reservedImages->toArray());

    }

}
