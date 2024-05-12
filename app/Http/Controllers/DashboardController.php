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

        $recent_invoice = DB::table('orders')->whereNotNull('total_price')->select(
            'orders.id as id',
            'orders.total_price as total_price',
            'orders.created_at as created_at',
            'orders.ten_khach_hang as customer_name'
        )->orderBy('orders.created_at')->get();

        return response()->json(
            [
                'message' => 'Successfully',
                'data' => [
                    "revenue" => $revenue,
                    "total_invoice" => $total_invoice,
                    "total_employee" => $total_employee,
                    "total_product" => $total_product,
                    "recent_invoice" => $recent_invoice
                ]
            ]
        );
    }
}
