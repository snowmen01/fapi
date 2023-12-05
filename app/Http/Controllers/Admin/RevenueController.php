<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Revenue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RevenueController extends Controller
{
    public function totalRevenue()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $previousMonth = date('m', strtotime('-1 month'));
        $previousYear = date('Y', strtotime('-1 month'));

        $currentMonthData = Order::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->whereNull('order_id')
            ->where('status', 4)
            ->where('status_payment', 1)
            ->sum('total');

        $previousMonthData = Order::whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousYear)
            ->whereNull('order_id')
            ->where('status', 4)
            ->where('status_payment', 1)
            ->sum('total');

        return response()->json([
            'statusCode' => 200,
            'dataCurrent' => $currentMonthData,
            'dataPrevious' => $previousMonthData,
        ], 200);
    }

    public function totalCustomer()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $previousMonth = date('m', strtotime('-1 month'));
        $previousYear = date('Y', strtotime('-1 month'));

        $currentMonthData = Customer::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count('id');

        $previousMonthData = Customer::whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousYear)
            ->count('id');

        return response()->json([
            'statusCode' => 200,
            'dataCurrent' => $currentMonthData,
            'dataPrevious' => $previousMonthData,
        ], 200);
    }

    public function selectOrderByStatus()
    {
        $data = Order::select('status', DB::raw('COUNT(id) as totalOrder'))
            ->whereNull('order_id')
            ->groupBy('status')
            ->orderByDesc('totalOrder')
            ->get();
        $data = $data->map(function ($dt) {
            $dt->status = config("constant.status_order_common.$dt->status");
            return $dt;
        });

        return response()->json([
            'statusCode' => 200,
            'data' => $data,
        ], 200);
    }

    public function selectRevenuesByPaymentType()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $codData = array_fill(0, 12, 0);
        $vnpayData = array_fill(0, 12, 0);

        for ($i = 1; $i <= $currentMonth; $i++) {
            $revenueData = Order::select('payment_type', DB::raw('SUM(total) as totalPrice'))
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', $currentYear)
                ->whereNull('order_id')
                ->where('status', 4)
                ->where('status_payment', 1)
                ->groupBy('payment_type')
                ->get();

            foreach ($revenueData as $payment) {
                $index = $i - 1;
                if ($payment->payment_type === '0') {
                    $codData[$index] = $payment->totalPrice;
                } elseif ($payment->payment_type === '1') {
                    $vnpayData[$index] = $payment->totalPrice;
                }
            }
        }

        $formatted = [
            [
                "label" => "COD",
                "data" => $codData,
            ],
            [
                "label" => "VNPAY",
                "data" => $vnpayData,
            ]
        ];

        return response()->json([
            'statusCode' => 200,
            'data' => $formatted,
        ], 200);
    }

    public function selectOrderRecent()
    {
        $data = Order::whereNull('order_id')
            ->with('customer')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();
        $data->map(function ($dt) {
            $dt->createdAt = date('d-m-Y H:i:s', strtotime($dt->created_at));
        });

        return response()->json([
            'statusCode' => 200,
            'data' => $data,
        ], 200);
    }

    public function totalProduct()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $previousMonth = date('m', strtotime('-1 month'));
        $previousYear = date('Y', strtotime('-1 month'));

        $currentMonthData = Customer::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count('id');

        $previousMonthData = Customer::whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousYear)
            ->count('id');

        return response()->json([
            'statusCode' => 200,
            'dataCurrent' => $currentMonthData,
            'dataPrevious' => $previousMonthData,
        ], 200);
    }

    public function totalOrder()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $previousMonth = date('m', strtotime('-1 month'));
        $previousYear = date('Y', strtotime('-1 month'));

        $currentMonthData = Order::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->whereNull('order_id')
            ->where('status', 4)
            ->where('status_payment', 1)
            ->count('id');

        $previousMonthData = Order::whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousYear)
            ->whereNull('order_id')
            ->where('status', 4)
            ->where('status_payment', 1)
            ->count('id');

        return response()->json([
            'statusCode' => 200,
            'dataCurrent' => $currentMonthData,
            'dataPrevious' => $previousMonthData,
        ], 200);
    }

    public function totalRevenueMonth()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $totalRevenueByMonth = [];

        for ($i = 1; $i <= $currentMonth; $i++) {
            $totalRevenueByMonth[date("F", mktime(0, 0, 0, $i, 1, $currentYear))] = Order::whereMonth('created_at', $i)
                ->whereYear('created_at', $currentYear)
                ->whereNull('order_id')
                ->where('status', 4)
                ->where('status_payment', 1)
                ->sum('total');
        }
        $formated = [];
        foreach ($totalRevenueByMonth as $key => $data) {
            $formated[] = [
                "name"  => "T" . date("n", strtotime($key)),
                "Tổng tiền" => $data,
            ];
        }

        return response()->json([
            'statusCode' => 200,
            'data' => $formated,
        ], 200);
    }

    public function topSaleProductMonth()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $topProducts = Order::select('product_id', DB::raw('SUM(quantity) as totalQuantity'))
            ->with('product', 'product.image')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('product_id')
            ->orderByDesc('totalQuantity')
            ->take(5)
            ->get();

        return response()->json([
            'statusCode' => 200,
            'data' => $topProducts,
        ], 200);
    }
}
