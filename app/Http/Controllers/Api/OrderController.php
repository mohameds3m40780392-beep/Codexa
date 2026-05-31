<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart_items;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        // 1. التحقق من الحقول المطلوبة بالصورة
        $request->validate([
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $userId = auth()->id();

        // 2. جلب عناصر السلة الخاصة بالمستخدم مع تفاصيل المنتج لـ (Total Price)
        $cartItems = Cart_items::with('product')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty'], 400);
        }

        // 3. حساب إجمالي السعر (Total Price)
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->quantity * ($item->product->price ?? 0);
        });

        // استخدام Transaction لضمان تنفيذ كل العمليات بنجاح أو إلغائها معاً في حال حدوث خطأ
        DB::beginTransaction();
        try {
            // 4. إنشاء الطلب الرئيسي (Order)
            $order = Order::create([
                'user_id' => $userId,
                'address' => $request->address,
                'phone' => $request->phone,
                'total_price' => $totalPrice,
            ]);

            // 5. نقل المنتجات من جدول السلة إلى جدول تفاصيل الطلب (Order Items)
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price, // حفظ السعر الحالي
                ]);
            }

            // 6. تفريغ عربة التسوق الخاصة بالمستخدم بعد نجاح العملية
            Cart_items::where('user_id', $userId)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }
}