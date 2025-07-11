<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\User;

class DashboardRepository
{
    public function getSummary()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return [
            'total_revenue' => Order::sum('total_amount'),

            'new_orders_this_week' => Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->count(),

            'customers' => User::where('role', 'user')->count(),
        ];
    }

    public function getMonthlyRevenue()
    {
        $year = 2025;

        // Lấy doanh thu theo tháng có đơn hàng
        $monthlyData = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('revenue', 'month'); 
        // Tạo mảng đủ 12 tháng
        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $result[] = [
                'month' => $i,
                'revenue' => isset($monthlyData[$i]) ? (float) $monthlyData[$i] : 0,
            ];
        }

        return $result;
    }

    public function getRevenueByCategory()
    {
        return DB::table('categories')
            ->leftJoin('products', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'order_details.product_id', '=', 'products.id')
            ->select(
                'categories.name as category',
                DB::raw('COALESCE(SUM(order_details.quantity), 0) as total_sold')
            )
            ->groupBy('categories.name')
            ->orderByDesc('total_sold')
            ->get();
    }

    public function getOrderStatusDistribution()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return DB::table('orders')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('status')
            ->get();
    }

    public function getTopSellingProducts()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->select('products.name', DB::raw('SUM(order_details.unit_price * order_details.quantity) as revenue'))
            ->groupBy('products.name')
            ->orderByDesc('revenue')
            ->limit(5) 
            ->get();
    }
}
