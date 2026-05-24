<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Voucher;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function summary(): JsonResponse
    {
        $data = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_vouchers' => Voucher::availableForFrontend()->count(),
            'total_customers' => User::role('user')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Dashboard summary retrieved successfully',
            'data' => $data
        ]);
    }
}