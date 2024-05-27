<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function get_statistical() {
        $revenue = DB::table('orders')->sum('total_price');
        $total_invoice = DB::table('orders')->whereNotNull('total_price')->count();   
        $total_employee = DB::table('users')->where('role', UserRole::STAFF)->count();
        $total_product = DB::table('san_pham')->count();
        $total_table = DB::table('tables')->count();

        $recent_invoice = DB::table('orders')->whereNotNull('total_price')->select(
            'orders.id as id',
            'orders.total_price as total_price',
            'orders.created_at as created_at',
            'orders.ten_khach_hang as customer_name'
        )->orderBy('orders.created_at')->get();

        $recent_invoice = DB::table('orders')->whereNotNull('total_price')->select(
            'orders.id as id',
            'orders.total_price as total_price',
            'orders.created_at as created_at',
            'orders.ten_khach_hang as customer_name'
        )->orderBy('orders.created_at')->get();

        $top_revenue_table = DB::table('orders')
                ->groupBy('table_id')
                ->join('tables', 'tables.id', '=', 'orders.table_id')
                ->join('setting_table', 'setting_table.id', '=', 'tables.setting_table_id')
                ->select(
                    'tables.id as table_id',
                    'tables.name as table_name',
                    DB::raw('SUM(total_price) as total_revenue'),
                    'setting_table.type as setting_table_type'
                )
                ->whereNotNull('orders.end_time')
                ->orderByDesc('total_revenue')
                ->get();

        return response()->json(
            [
                'message' => 'Successfully',
                'data' => [
                    "revenue" => $revenue,
                    "total_invoice" => $total_invoice,
                    "total_employee" => $total_employee,
                    "total_product" => $total_product,
                    "recent_invoice" => $recent_invoice,
                    "top_revenue_table" => $top_revenue_table,
                    "total_table" => $total_table
                ]
            ]
        );
    }
}
