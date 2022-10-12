<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\PayRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\Payment\PaymentServiceManager;
use App\Services\Payment\Requests\IDPayRequest;
use Exception;
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
            $products = Product::findMany(array_keys($orderItems));
            $productsTotalPrice = $products->sum('price');
            $refCode = sha1(time() . rand(1111, 9999));

            $createdOrder = Order::create([
                'user_id' => $createdUser->id,
                'amount' => $productsTotalPrice,
                'ref_code' => $refCode,
                'status' => 'unpaid'
            ]);

            $orderItemsForCreatedOrder = $products->map(function ($product) {
                $currentProduct = $product->only(['price', 'id']);
                $currentProduct['product_id'] = $currentProduct['id'];
                unset($currentProduct['id']);
                return $currentProduct;
            });

            $createdOrder->orderItems()->createMany($orderItemsForCreatedOrder->toArray());

            $resId = sha1(time() . rand(1111, 9999));
            $refId = sha1(time() . rand(1111, 9999));

            $createdPayment = Payment::create([
                'order_id' => $createdOrder->id,
                'status' => 'unpaid',
                'gateway' => 'idpay',
                'res_id' => $resId,
                'ref_id' => $refId,
            ]);

            $idPayRequest = new IDPayRequest([
                'user' => $createdUser,
                'amount' => $productsTotalPrice,
                'orderId' => $createdOrder->id
            ]);

            $paymentService = new PaymentServiceManager(PaymentServiceManager::IDPAY, $idPayRequest);

            return $paymentService->pay();

        } catch (Exception $e) {
            return back()->with('failed', $e->getMessage());
        }

    }

    public function callback()
    {

    }

}
